<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnProductController extends Controller
{
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
        $returnProductJoined = DB::table('return_product')
            ->join('user', 'return_product.user_id', '=', 'user.user_id')
            ->join('product', 'product.return_product_id', '=', 'return_product.return_product_id')
            ->select('return_product.*', 'user.*', 'product.*')
            ->get();

        $inventory = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id') // join with product to filter by specific product_id
            ->select('inventory.*') // select necessary fields
            ->whereIn('product.product_id', $returnProductJoined->pluck('product_id')) // fetch only products in sales
            ->get();    

        // Decode the description for each inventory item
        foreach ($returnProductJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        // Pass the inventory managers and user role to the view
        return view('return_product.return_product_table', [
            'userSQL' => $userSQL,
            'returnProductJoined' => $returnProductJoined,
            'inventory' => $inventory
        ]);
    }

    public function showReturnForm($id)
    {
        return view('sales/return_product');
    }

    private function generateId($table)
    {
        // Generate a random 8-digit number
        do {
            $id = random_int(10000000, 99999999);
        } while (DB::table($table)->where("{$table}_id", $id)->exists()); // Ensure the ID is unique

        return $id;
    }

    public function processReturn(Request $request)
{
    $validatedData = $request->validate([
        'return_quantity' => 'required|integer|min:1',
        'return_reason' => 'required|string|max:255',
    ]);

    // Get the user ID (assuming the user is logged in)
    $userId = Auth::id();

    // Start a transaction to ensure consistency between sales and inventory updates
    DB::transaction(function () use ($userId, $validatedData, $request) {
        // Generate a new ID for the return product
        $newReturnProductId = $this->generateId('return_product');

        // Insert the return record
        DB::table('return_product')->insert([
            'return_product_id' => $newReturnProductId, // Use the generated ID here
            'user_id' => $userId,
            'return_quantity' => $validatedData['return_quantity'],
            'return_reason' => $validatedData['return_reason'],
            'return_date' => now(), // Current timestamp
        ]);

        // Update the product with the new return_product_id
        DB::table('product')
            ->where('product_id', $request->product_id)
            ->update([
                'return_product_id' => $newReturnProductId, // Use the same generated ID here
            ]);
    });

    return redirect()->route('sales_table')->with('success', 'Product returned successfully');
}


    public function showRefundExchangeForm()
    {
        // Get stored session data
        $quantity = session('quantity');
        $product = Product::where('product_name', session('product_name'))->first();
        $inventory = Inventory::where('product_id', $product->product_id)->first();

        $totalRefundAmount = $inventory->sale_price_per_unit * $quantity;

        return view('refund-exchange', [
            'product_name' => $product->product_name,
            'total_refund' => $totalRefundAmount
        ]);
    }

    public function processRefundOrExchange(Request $request)
    {
        if ($request->input('action') === 'refund') {
            // Process refund logic
            return redirect()->back()->with('success', 'Product refunded successfully.');
        } elseif ($request->input('action') === 'exchange') {
            // Logic to exchange the product
            return redirect()->back()->with('success', 'Product exchanged successfully.');
        }

        return back()->withErrors(['Invalid action']);
    }
}
