<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contact_Details;
use App\Models\Credentials;
use App\Models\Products;
use App\Models\Categories;
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

        // Join `user`, `credentials`, and `contact_details` to get Inventory Manager details
        $userJoined = DB::table('user')
        ->join('credentials', 'user.credential_id', '=', 'credentials.credential_id')
        ->join('contact_details', 'user.contact_id', '=', 'contact_details.contact_id')
        ->select('user.*', 'credentials.*', 'contact_details.*')
        ->where('credentials.role', '!=', 'Administrator') // Only select Inventory Managers
        ->get();

        $inventoryJoined = DB::table('products')
        // ->join('credentials', 'user.credential_id', '=', 'credentials.credential_id')
        ->join('categories', 'products.category_id', '=', 'categories.category_id')
        ->select('products.*', 'categories.*')
        // ->where('credentials.role', '!=', 'Administrator') // Only select Inventory Managers
        ->get();

        // Pass the inventory managers and user role to the view
        return view('inventory.products_table', [
            'userJoined' => $userJoined,
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
        return view('inventory.create_product');
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
            'product_name' => ['required', 'string', 'max:255'],
            'category_name' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'numeric'],
            'UoM' => ['required', 'string', 'max:255'],
            'quantity_in_stock' => ['required', 'numeric'],
            'reorder_level' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        // Format the unit_price to include commas and 2 decimal places
        $formattedUnitPrice = number_format($validatedData['unit_price'], 2, '.', ',');

        // Use a transaction to ensure data integrity
        DB::transaction(function () use ($validatedData, $formattedUnitPrice) {
            // Create the Contact_Details first
            $category = Categories::create([
                'category_name' => $validatedData['category_name'],
            ]);

            // Store user
            return Products::create([
                'product_name' => $validatedData['product_name'],
                'description' => $validatedData['description'],
                'unit_price' => $formattedUnitPrice,
                'UoM' => $validatedData['UoM'],
                'quantity_in_stock' => $validatedData['quantity_in_stock'],
                'reorder_level' => $validatedData['reorder_level'],
                'category_id' => $category->category_id,
            ]);
        });
        // Redirect or return response after successful creation
    return redirect()->route('products_table')->with('success', 'Product added successfully.');
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
        // Check if the user is logged in
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Unauthorized Page');
        }

        // Find the product by its ID
        $product = Products::find($id);

        // Check if the product exists
        if (!$product) {
            return redirect()->route('products_table')->with('error', 'Product not found.');
        }

        // Find the associated category
        $category = Categories::find($product->category_id);

        // Pass the product and category data to the edit view
        return view('inventory.update_product', [
            'product' => $product,
            'category' => $category,
        ]);
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
        // Validate the incoming request data
        $validatedData = $request->validate([
            'product_name' => ['required', 'string', 'max:30'],
            'category_name' => ['required', 'string', 'max:30'],
            'unit_price' => ['required', 'numeric'],
            'UoM' => ['required', 'string', 'max:30'],
            'quantity_in_stock' => ['required', 'numeric'],
            'reorder_level' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $formattedUnitPrice = number_format($validatedData['unit_price'], 2, '.', ',');

        // Use a transaction for updating the product and category
        DB::transaction(function () use ($validatedData, $formattedUnitPrice, $id) {
            // Find the product by id
            $product = Products::find($id); // Get the product instance

            if (!$product) {
                throw new Exception('Product not found'); // Handle case where product is not found
            }

            // Update the product details
            $product->update([
                'product_name' => $validatedData['product_name'],
                'quantity_in_stock' => $validatedData['quantity_in_stock'],
                'reorder_level' => $validatedData['reorder_level'],
                'unit_price' => $formattedUnitPrice,
                'UoM' => $validatedData['UoM'],
                'description' => $validatedData['description'],
            ]);

            // Update the associated category
            $category = Categories::find($product->category_id); // Get the associated category

            if ($category) {
                $category->update([
                    'category_name' => $validatedData['category_name'],
                ]);
            } else {
                throw new Exception('Category not found'); // Handle case where category is not found
            }
        });

        // Redirect back to products_table page with success message
        return redirect()->route('products_table')->with('success', 'Product updated successfully.');
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
            // Validate admin credentials
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);
    
            // Get the authenticated admin user
            $user = auth()->user();
    
            // Check if the admin credentials are correct
            if ($user->credential->username !== $request->username || 
                !Hash::check($request->password, $user->credential->password)) {
                return redirect()->route('products_table')->with('error', 'Invalid user credentials.');
            }
    
            // Find the user to be deleted
            $product = Products::find($id);
    
            // Check if the user exists
            if (!$product) {
                return redirect()->route('products_table')->with('error', 'User not found.');
            }
    
            // Delete the related credentials and contact details
            if ($product->category) {
                $product->category->delete();
            }
    
            // Finally, delete the user
            $product->delete();
    
            return redirect()->route('products_table')->with('success', 'User deleted successfully.');
        }
    
    }
}
