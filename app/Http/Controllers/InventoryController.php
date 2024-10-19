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
        return view('inventory.products_table', [
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
        'company_name' => ['required', 'string', 'max:30'],
        'contact_person' => ['required', 'string', 'max:30'],
        'mobile_number' => ['required', 'numeric'],
        'email' => ['required', 'string', 'max:30'],
        'address' => ['required', 'string', 'max:50'],
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
            'description' => json_encode([ // Ensure you encode the array as JSON
                    'color' => $validatedData['color'],
                    'size' => $validatedData['size'],
                    'description' => $validatedData['description'],
                ]),
            'category_id' => $category->category_id, // Use the generated category_id
            'supplier_id' => $supplier->supplier_id, // Use the generated supplier_id
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
    return redirect()->route('products_table')->with('success', 'Product added successfully.');
}



    /**
     * Generate a custom ID for the specified table.
     *
     * @param string $table
     * @return int
     */
    private function generateId($table)
    {
        // Define the maximum ID value
        $maxIdValue = 8;

        // Get the highest existing ID for the specified table
        $currentMaxId = null;
        switch ($table) {
            case 'product':
                $currentMaxId = Product::max('product_id');
                break;
            case 'category':
                $currentMaxId = Category::max('category_id');
                break;
            case 'supplier':
                $currentMaxId = Supplier::max('supplier_id');
                break;
            case 'inventory':
                $currentMaxId = Inventory::max('inventory_id');
                break;
        }

        // Determine the new ID
        $newId = ($currentMaxId !== null && $currentMaxId < $maxIdValue) ? $currentMaxId + 1 : 1;

        // Ensure the new ID does not exceed the maximum allowed value
        return min($newId, $maxIdValue);
    }

    public function restock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory = Inventory::where('product_id', $validated['product_id'])->firstOrFail();

        // Increase the in_stock value
        $inventory->increment('in_stock', $validated['quantity']);

        return response()->json(['message' => 'Stock updated successfully']);
    }

    public function transferStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_stockroom_id' => 'required|exists:stockrooms,id',
            'to_stockroom_id' => 'required|exists:stockrooms,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Adjust the stock quantities in the respective stockrooms
        StockTransfer::create($validated);

        $inventory = Inventory::where('product_id', $validated['product_id'])->firstOrFail();
        $inventory->decrement('in_stock', $validated['quantity']);
        
        return response()->json(['message' => 'Stock transferred successfully']);
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
        $product = Product::find($id);

        // Check if the product exists
        if (!$product) {
            return redirect()->route('products_table')->with('error', 'Product not found.');
        }

        $inventory = Inventory::find($id);
        // Find the associated category
        $category = Category::find($product->category_id);
        $supplier = Supplier::find($product->supplier_id);

            $descriptionArray = json_decode($product->description, true); // Decode the JSON description into an array
        

        // Pass the product and category data to the edit view
        return view('inventory.update_product', [
            'inventory' => $inventory,
            'product' => $product,
            'category' => $category,
            'supplier' =>  $supplier,
            'descriptionArray' => $descriptionArray
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
            'purchase_price_per_unit' => ['required', 'numeric'],
            'sale_price_per_unit' => ['required', 'numeric'],
            'unit_of_measure' => ['required', 'string', 'max:15'],
            'in_stock' => ['required', 'numeric'],
            'reorder_level' => ['required', 'numeric'],
            'color' => 'required|string|max:50',
            'size' => 'required|string|max:50',
            'description' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:30'],
            'contact_person' => ['required', 'string', 'max:30'],
            'mobile_number' => ['required', 'numeric'],
            'email' => ['required', 'email', 'max:30'], // Corrected email validation
            'address' => ['required', 'string', 'max:50'],
        ]);

        $formattedPurchasePrice = number_format($validatedData['purchase_price_per_unit'], 2, '.', ',');
        $formattedSalePrice = number_format($validatedData['sale_price_per_unit'], 2, '.', ',');

        DB::transaction(function () use ($validatedData, $formattedPurchasePrice, $formattedSalePrice, $id) {
            // Find the product by id
            $product = Product::find($id); // Get the product instance

            if (!$product) {
                throw new Exception('Product not found'); // Handle case where product is not found
            }

            // Update the product details
            $product->update([
                'product_name' => $validatedData['product_name'],
                'description' => json_encode([ // Ensure you encode the array as JSON
                    'color' => $validatedData['color'],
                    'size' => $validatedData['size'],
                    'description' => $validatedData['description'],
                ]), 
            ]);

            // Update the associated category
            $category = Category::find($product->category_id); // Get the associated category
            if ($category) {
                $category->update([
                    'category_name' => $validatedData['category_name'],
                ]);
            } else {
                throw new Exception('Category not found'); // Handle case where category is not found
            }

            $category = Category::find($product->category_id); // Get the associated category
            if ($category) {
                $category->update([
                    'category_name' => $validatedData['category_name'],
                ]);
            } else {
                throw new Exception('Category not found'); // Handle case where category is not found
            }

            $supplier = Supplier::find($product->supplier_id); // Get the associated supplier
            if ($supplier) {
                $supplier->update([
                    'company_name' => $validatedData['company_name'],
                    'contact_person' => $validatedData['contact_person'],
                    'mobile_number' => $validatedData['mobile_number'],
                    'email' => $validatedData['email'],
                    'address' => $validatedData['address'],
                ]);
            } else {
                throw new Exception('Supplier not found'); // Handle case where supplier is not found
            }

            $inventory = Inventory::find($id);
            if ($inventory) {
                $inventory->update([
                    'purchase_price_per_unit' => $formattedPurchasePrice,
                    'sale_price_per_unit' => $formattedSalePrice,
                    'unit_of_measure' => $validatedData['unit_of_measure'],
                    'in_stock' => $validatedData['in_stock'],
                    'reorder_level' => $validatedData['reorder_level']
                ]);
            } else {
                throw new Exception('Inventory not found'); // Handle case where inventory is not found
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
