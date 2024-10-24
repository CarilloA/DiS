<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnProductController extends Controller
{
    public function index()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect('/login')->withErrors('You must be logged in.');
        }

        // Fetch Inventory Manager details
        $userSQL = DB::table('user')
            ->select('user.*')
            ->where('role', '=', 'Inventory Manager')
            ->get();

        // Join 'return_product', 'user', and 'sales' tables
        $returnProductJoined = DB::table('return_product')
            ->join('user', 'return_product.user_id', '=', 'user.user_id')
            ->join('sales', 'sales.return_product_id', '=', 'return_product.return_product_id')
            ->join('product', 'sales.product_id', '=', 'product.product_id')
            ->select('return_product.*', 'user.*', 'sales.*', 'product.*')
            ->get();

        // Decode the description array for each return product item
        foreach ($returnProductJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        // Pass necessary data to the view
        return view('return_product.return_product_table', [
            'userSQL' => $userSQL,
            'returnProductJoined' => $returnProductJoined,
        ]);
    }

    public function showReturnForm()
    {
        return view('return_product.create_return_product');
    }

    private function generateId($table)
    {
        do {
            $id = random_int(10000000, 99999999);
        } while (DB::table($table)->where("{$table}_id", $id)->exists()); // Ensure unique ID

        return $id;
    }

    public function processReturn(Request $request)
{
    // Validate the input
    $validatedData = $request->validate([
        'return_quantity' => 'required|integer|min:1',
        'total_return_amount' => 'required',
        'return_reason' => 'required|string|max:255',
    ]);

    $userId = Auth::id(); // Get the logged-in user's ID

    // Start a transaction to maintain database integrity
    DB::transaction(function () use ($userId, $validatedData, $request) {
        // Generate a unique ID for the return product
        $newReturnProductId = $this->generateId('return_product');

        // Create the new return record into the 'return_product' table
        DB::table('return_product')->insert([
            'return_product_id' => $newReturnProductId,
            'user_id' => $userId,
            'return_quantity' => $validatedData['return_quantity'],
            'total_return_amount' => $validatedData['total_return_amount'],
            'return_reason' => $validatedData['return_reason'],
            'return_date' => now(), // Current timestamp
        ]);

        // Fetch the sales record based on the provided sales_id
        $sales = DB::table('sales')
            ->where('sales_id', $request->sales_id)
            ->first();

        // Check if the sales record exists
        if ($sales) {
            // Ensure the sales quantity does not go below zero after the return
            $newQuantity = $sales->quantity - $validatedData['return_quantity'];
            $newTotalAmount = $sales->total_amount - $validatedData['total_return_amount'];

            if ($newQuantity < 0) {
                // Abort the transaction and return an error if the new quantity is negative
                throw new \Exception('Returned quantity exceeds the available sales quantity.');
            }

            // Update the sales record with the new quantity and link the return_product_id
            DB::table('sales')
                ->where('sales_id', $sales->sales_id)
                ->update([
                    'quantity' => $newQuantity, // Decrement sale quantity
                    'total_amount' => $newTotalAmount, // Decrement sale total amount
                    'return_product_id' => $newReturnProductId, // Link the return product
                ]);
        } else {
            // Abort the transaction and throw an error if the sales record is not found
            throw new \Exception('Sales record not found.');
        }
    });

    // Redirect back to the return product table with a success message
    return redirect()->route('return_product_table')->with('success', 'Product returned successfully.');
}




    public function showRefundExchangeForm()
    {
        // Get stored session data
        // $quantity = session('quantity');
        // $product = Product::where('product_name', session('product_name'))->first();
        // $inventory = Inventory::where('product_id', $product->product_id)->first();

        // $totalRefundAmount = $inventory->sale_price_per_unit * $quantity;

        // return view('refund-exchange', [
        //     'product_name' => $product->product_name,
        //     'total_refund' => $totalRefundAmount
        // ]);
    }

    public function processRefundOrExchange(Request $request)
    {
        // if ($request->input('action') === 'refund') {
        //     // Process refund logic
        //     return redirect()->back()->with('success', 'Product refunded successfully.');
        // } elseif ($request->input('action') === 'exchange') {
        //     // Logic to exchange the product
        //     return redirect()->back()->with('success', 'Product exchanged successfully.');
        // }

        // return back()->withErrors(['Invalid action']);
    }
}
