<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\ConfirmRegistration;
use Illuminate\Support\Facades\Mail;
 use Illuminate\Support\Facades\Log;
 use Exception;

class InventoryController extends Controller
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

        $inventoryJoined = DB::table('inventory')
        // ->join('credentials', 'user.credential_id', '=', 'credentials.credential_id')
        ->join('product', 'inventory.product_id', '=', 'product.product_id')
        ->join('category', 'product.category_id', '=', 'category.category_id')
        ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
        ->select('inventory.*', 'product.*', 'category.*', 'supplier.*')
        // ->where('credentials.role', '!=', 'Administrator') // Only select Inventory Managers
        ->get();

        // Decode the description for each inventory item
        foreach ($inventoryJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true); // Decode the JSON description into an array
        }

            // Pass the inventory managers and user role to the view
            return view('inventory.inventory_table', [
                'userSQL' => $userSQL,
                'inventoryJoined' => $inventoryJoined,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }



    /**
     * Generate a custom ID for the specified table.
     *
     * @param string $table
     * @return int
     */
    private function generateId($table)
    {
        //
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
    public function destroy(Request $request, $id)
    {
        {
            // Validate login credentials
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);
    
            // Get the authenticated login user
            $user = auth()->user();
    
            // Check if the user credentials are correct
            if ($user->username !== $request->username || 
                !Hash::check($request->password, $user->password)) {
                return redirect()->route('products_table')->with('error', 'Invalid user credentials.');
            }
    
            // Find row to be deleted
            $product = Product::find($id);
            $inventory = Inventory::find($id);
    
            // Check if the product exists
            if (!$product) {
                return redirect()->route('products_table')->with('error', 'Product not found.');
            }

            if (!$inventory) {
                return redirect()->route('products_table')->with('error', 'Inventory not found.');
            }

            // Delete Contents
            if ($inventory->product) {
                $inventory->product->delete();
            }
    
            // Delete Contents
            if ($product->category) {
                $product->category->delete();
            }

            // Delete Contents
            if ($product->supplier) {
                $product->supplier->delete();
            }
    
            // Finally, delete the inventory
            $inventory->delete();
    
            return redirect()->route('products_table')->with('success', 'Product deleted successfully.');
        }
    
    }
}
