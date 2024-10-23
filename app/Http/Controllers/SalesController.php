<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check if the user is logged in
        if (!Auth::check()) {
            // If the user is not logged in, redirect to login
            return redirect('/login')->withErrors('You must be logged in.');
        }

        // Get Inventory Manager details
        $userSQL = DB::table('user')
            ->select('user.*')
            ->where('role', '=', 'Inventory Manager')
            ->get();

        // Join tables to get sales
        $salesJoined = DB::table('sales')
            ->join('user', 'sales.user_id', '=', 'user.user_id')
            ->join('product', 'sales.product_id', '=', 'product.product_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->select('sales.*', 'user.*', 'product.*', 'category.*')
            ->get();

        $inventory = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id') // join with product to filter by specific product_id
            ->select('inventory.*') // select necessary fields
            ->whereIn('product.product_id', $salesJoined->pluck('product_id')) // fetch only products in sales
            ->get();    

        // Decode the description for each inventory item
        foreach ($salesJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        $deadline = now()->subDays(7);

        // Pass the inventory managers and user role to the view
        return view('sales.sales_table', [
            'userSQL' => $userSQL,
            'salesJoined' => $salesJoined,
            'deadline' => $deadline,
            'inventory' => $inventory
        ]);
    }

    private function generateId($table)
    {
        // Generate a random 8-digit number
        do {
            $id = random_int(10000000, 99999999);
        } while (DB::table($table)->where("{$table}_id", $id)->exists()); // Ensure the ID is unique

        return $id;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Fetch categories and their products
        $productJoined = DB::table('product')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->select('product.*', 'category.category_id', 'category.category_name') // Ensure you're selecting necessary fields
            ->get();

        return view('sales.create_sales', ['productJoined' => $productJoined]);
    }

    public function fetchProduct(Request $request)
    {
        $product_id = $request->input('product_id');
        $product = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->select('inventory.*', 'product.*', 'category.category_name')
            ->where('product.product_id', $product_id)
            ->first();

        if ($product) {
            // Decode the description JSON if available
            $product->descriptionArray = json_decode($product->description, true);
            $seller = Auth::user()->first_name . ' ' . Auth::user()->last_name; // Logged-in seller's full name
            return response()->json([
                'success' => true,
                'product' => $product,
                'seller' => $seller
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Product not found.']);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'product_id' => 'required|integer|exists:product,product_id',
            'quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric'
        ]);

        // Get the user ID (assuming the user is logged in)
        $userId = Auth::id();

        // Retrieve the product and inventory data
        $product = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->where('product.product_id', $request->product_id)
            ->first();

        // Ensure the product exists and has enough stock
        if (!$product || $product->in_stock < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }

        // Start a transaction to ensure consistency between sales and inventory updates
        DB::transaction(function () use ($userId, $validatedData, $request) {
            // Insert the sale record
            DB::table('sales')->insert([
                'sales_id' => $this->generateId('sales'), // Generate custom ID for sales
                'user_id' => $userId,
                'product_id' => $validatedData['product_id'],
                'quantity' => $validatedData['quantity'],
                'total_amount' => $validatedData['total_amount'],
                'sales_date' => now(), // Current timestamp
            ]);

            // Update the inventory: subtract from in_stock and add to out_stock
            DB::table('inventory')
                ->where('product_id', $request->product_id)
                ->update([
                    'in_stock' => DB::raw('in_stock - ' . $validatedData['quantity']),
                ]);
        });

        return redirect()->route('sales_table')->with('success', 'Sale completed successfully');
    }

    public function search(Request $request)
{
    if ($request->ajax()) {
        // Get the search input from the request
        $search = $request->get('query');

        // Query the sales table with necessary joins
        $salesQuery = DB::table('sales')
            ->join('user', 'sales.user_id', '=', 'user.user_id')
            ->join('product', 'sales.product_id', '=', 'product.product_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->select('sales.*', 'user.first_name', 'user.last_name', 'product.product_name', 'product.description', 'category.category_name');

        // Apply search filter if search query is present
        if (!empty($search)) {
            $salesQuery->where(function($query) use ($search) {
                $query->where('sales.sales_id', 'LIKE', "%{$search}%")
                      ->orWhere('user.first_name', 'LIKE', "%{$search}%")
                      ->orWhere('user.last_name', 'LIKE', "%{$search}%")
                      ->orWhere('product.product_name', 'LIKE', "%{$search}%")
                      ->orWhere('category.category_name', 'LIKE', "%{$search}%");
            });
        }

        // Execute the query and get the results
        $sales = $salesQuery->get();

        // Decode the description JSON for each product
        foreach ($sales as $sale) {
            $sale->descriptionArray = json_decode($sale->description, true);
        }

        // Return the results as a JSON response
        return response()->json($sales);
    }

    return view('sales_table');
}



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Implement logic to display a specific sales order
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Implement logic to show edit form for a specific sales order
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Implement logic to update a specific sales order
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Implement logic to delete a specific sales order
    }
}
