<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Stockroom;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class PurchaseController extends Controller
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

        // SQL `user` to get Inventory Manager details
        $userSQL = DB::table('user')
        ->select('user.*')
        ->where('role', '=', 'Inventory Manager')
        ->get();

        $productJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Filter duplicates based on a unique key (e.g., product_id)
        $productJoined = $productJoined->unique('product_id');

        // Decode the description for each inventory item
        foreach ($productJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true); // Decode the JSON description into an array
        }

        // Pass the inventory managers and user role to the view
        return view('purchase.purchase_table', [
            'userSQL' => $userSQL,
            'productJoined' => $productJoined,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('purchase.create_product');
    }

    /**
     * Generate a unique 8-digit ID for the given table.
     *
     * @param  string $table
     * @return int
     */
    private function generateId($table)
    {
        // Generate a random 8-digit number
        do {
            $id = random_int(10000000, 99999999);
        } while (DB::table($table)->where("{$table}_id", $id)->exists()); // Ensure the ID is unique

        return $id;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'product_name' => ['required', 'string', 'max:30'],
            'category_name' => ['required', 'string', 'max:30'],
            'purchase_price_per_unit' => ['required', 'numeric'],
            'sale_price_per_unit' => ['required', 'numeric'],
            'unit_of_measure' => ['required', 'string', 'max:15'],
            'in_stock' => ['required', 'numeric'],
            'reorder_level' => ['required', 'numeric'],
            'color' => ['max:50'],
            'size' => ['max:50'],
            'description' => ['max:255'],
            'company_name' => ['string', 'max:30'],
            'contact_person' => ['string', 'max:30'],
            'mobile_number' => ['numeric'],
            'email' => ['string', 'max:30'],
            'address' => ['required', 'string', 'max:50'],
            'aisle_number' => ['numeric'],
            'cabinet_level' => ['numeric'],
            'product_quantity' => ['numeric'],
        ]);

        // Use a transaction to ensure data integrity
        DB::transaction(function () use ($validatedData) {
            // Create the Category first
            $category = Category::create([
                'category_id' => $this->generateId('category'), // Generate custom ID for category
                'category_name' => $validatedData['category_name'],
            ]);

            // Create the Supplier
            $supplier = Supplier::create([
                'supplier_id' => $this->generateId('supplier'), // Generate custom ID for supplier
                'company_name' => $validatedData['company_name'],
                'contact_person' => $validatedData['contact_person'],
                'mobile_number' => $validatedData['mobile_number'],
                'email' => $validatedData['email'],
                'address' => $validatedData['address'],
            ]);

            // Create the Product
            $product = Product::create([
                'product_id' => $this->generateId('product'), // Generate custom ID for product
                'product_name' => $validatedData['product_name'],
                'description' => json_encode([ // Encode the array as JSON
                    'color' => $validatedData['color'],
                    'size' => $validatedData['size'],
                    'description' => $validatedData['description'],
                ]),
                'category_id' => $category->category_id, // Use the generated category_id
                'supplier_id' => $supplier->supplier_id, // Use the generated supplier_id
            ]);

            // Create the Stockroom
            $stockroom = Stockroom::create([
                'stockroom_id' => $this->generateId('stockroom'), // Generate custom ID for product
                'aisle_number' => $validatedData['aisle_number'],
                'cabinet_level' => $validatedData['cabinet_level'],
                'product_quantity' => $validatedData['product_quantity'],
                'category_id' => $category->category_id, // Use the generated category_id
            ]);

            // Create the StockTransfer
            StockTransfer::create([
                'stock_transfer_id' => $this->generateId('stock_transfer'), // Generate custom ID for product
                'transfer_quantity' => $validatedData['product_quantity'],
                'transfer_date' => now(),
                'product_id' => $product->product_id, // Use the generated category_id
                'user_id' => Auth::user()->user_id, // Use the logged in user_id
                'to_stockroom_id' => $stockroom->stockroom_id, // Use the generated stockroom_id
            ]);

            // Create the Inventory
            Inventory::create([
                'inventory_id' => $this->generateId('inventory'), // Generate custom ID for inventory
                'purchase_price_per_unit' => $validatedData['purchase_price_per_unit'],
                'sale_price_per_unit' => $validatedData['sale_price_per_unit'],
                'unit_of_measure' => $validatedData['unit_of_measure'],
                'in_stock' => $validatedData['in_stock'],
                'reorder_level' => $validatedData['reorder_level'],
                'product_id' => $product->product_id, // Use the generated product_id
            ]);
        });

        // Redirect or return response after successful creation
        return redirect()->route('purchase_table')->with('success', 'Product added successfully.');
    }

    public function restock(Request $request) 
{
    // Validate incoming request data
    $validatedData = $request->validate([
        'product_id' => ['required', 'exists:product,product_id'],
        'purchase_price_per_unit' => ['required', 'numeric'],
        'sale_price_per_unit' => ['required', 'numeric'],
        'unit_of_measure' => ['required', 'string', 'max:15'],
        'quantity' => ['required', 'numeric', 'min:1'],
        'update_supplier' => ['nullable', 'boolean'],
        'supplier_id' => 'required|exists:supplier,supplier_id',
        'stockroom_id' => ['required', 'exists:stockroom,stockroom_id'],
    ]);

    // Use DB transaction to ensure data integrity
    DB::transaction(function () use ($validatedData, $request) {
        
        // Update Inventory
        $inventory = Inventory::where('product_id', $validatedData['product_id'])->firstOrFail();

        // Update inventory details
        $inventory->update([
            'purchase_price_per_unit' => $validatedData['purchase_price_per_unit'],
            'sale_price_per_unit' => $validatedData['sale_price_per_unit'],
            'unit_of_measure' => $validatedData['unit_of_measure'],
            'in_stock' => $inventory->in_stock + $validatedData['quantity'], // Increment stock
        ]);

        $userId = Auth::id();

        // Insert into stock_transfer
        DB::table('stock_transfer')->insert([
            'stock_transfer_id' => $this->generateId('stock_transfer'),
            'transfer_quantity' => $validatedData['quantity'],
            'transfer_date' => now(),
            'product_id' => $validatedData['product_id'],
            'user_id' => $userId,
            'to_stockroom_id' => $validatedData['stockroom_id'],
        ]);

        // Update Stockroom
        $stockroom = Stockroom::where('stockroom_id', $validatedData['stockroom_id'])->firstOrFail();
        // Update stockroom details
        $stockroom->update([
            'product_quantity' => $validatedData['quantity'],
        ]);

        // Check if the supplier details need to be updated
        if ($validatedData['update_supplier'] == true) {
            // Validate supplier data
            $suppliervalidatedData = $request->validate([
                'company_name' => 'required|string',
                'contact_person' => 'required|string',
                'mobile_number' => 'required|numeric',
                'email' => 'required|email',
                'address' => 'required|string',
            ]);

            // Update supplier information if requested
            Supplier::where('supplier_id', $validatedData['supplier_id'])->update($suppliervalidatedData);
        }
    });

    // Return success response
    return redirect()->route('purchase_table')->with('success', 'Restock successful.');
}

public function restockStoreProduct(Request $request) 
{
    // Validate incoming request data
    $validatedData = $request->validate([
        'product_id' => ['required', 'exists:product,product_id'],
        'stockroom_id' => ['required', 'exists:stockroom,stockroom_id'],
        'transfer_quantity' => ['required', 'numeric', 'min:1'],
        'product_quantity' => ['required', 'numeric', 'min:1'],
    ]);

    // Use DB transaction to ensure data integrity
    DB::transaction(function () use ($validatedData, $request) {

        // Get the user ID (assuming the user is logged in)
        $userId = Auth::id();

        // Insert into stock_transfer
        DB::table('stock_transfer')->insert([
            'stock_transfer_id' => $this->generateId('stock_transfer'),
            'transfer_quantity' => $validatedData['transfer_quantity'],
            'transfer_date' => now(),
            'product_id' => $validatedData['product_id'],
            'user_id' => $userId,
            'from_stockroom_id' => $validatedData['stockroom_id'],
        ]);

        // Update Stockroom
        $stockroom = Stockroom::where('stockroom_id', $validatedData['stockroom_id'])->firstOrFail();

        $productQuantity = $validatedData['product_quantity'] - $validatedData['transfer_quantity'];

        // Update inventory details
        $stockroom->update([
            'product_quantity' => $productQuantity,
        ]);

        
    });

    // Return success response
    return redirect()->route('purchase_table')->with('success', 'Restock successful.');
}


    





    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
