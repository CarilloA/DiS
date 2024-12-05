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
        ->join('product', 'inventory.product_id', '=', 'product.product_id')
        ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
        ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
        ->join('category', 'product.category_id', '=', 'category.category_id')
        ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
        ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
        ->orderBy('updated_at', 'desc')
        ->distinct()
        ->get();

        // Optionally, if the `distinct()` doesn't solve the problem, you can filter by unique `product_id`
        $inventoryJoined = $inventoryJoined->unique('product_id');

        // Decode the description for each inventory item
        foreach ($inventoryJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true); // Decode the JSON description into an array
        }

        // Fetch all categories for filtering or display purposes
        $categories = Category::all();

        // Fetch all categories for filtering or display purposes
        $suppliers = Supplier::all();

        return view('inventory.inventory_table', [
            'userSQL' => $userSQL,
            'inventoryJoined' => $inventoryJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }

    
    public function productNameFilter(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->withErrors('You must be logged in.');
        }
    
        // SQL `user` to get Inventory Manager details
        $userSQL = DB::table('user')
            ->select('user.*')
            ->where('role', '=', 'Inventory Manager')
            ->get();
    
        $selectedLetters = $request->get('letters', []); // Get selected letters from the request
    
        // Build the query with a letter filter
        $inventoryQuery = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->orderBy('product.product_name', 'asc')
            ->distinct();
    
        // Apply filtering by letters if any letters are selected
        if (!empty($selectedLetters)) {
            $inventoryQuery->where(function ($query) use ($selectedLetters) {
                foreach ($selectedLetters as $letter) {
                    $query->orWhere('product.product_name', 'like', $letter . '%');
                }
            });
        }
    
        $inventoryJoined = $inventoryQuery->get();
        $inventoryJoined = $inventoryJoined->unique('product_id');
    
        // Decode description for each inventory item
        foreach ($inventoryJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }
    
        // Fetch categories and suppliers for filtering or display purposes
        $categories = Category::all();
        $suppliers = Supplier::all();
    
        return view('inventory.inventory_table', [
            'userSQL' => $userSQL,
            'inventoryJoined' => $inventoryJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }
    


    public function CategoryFilter(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->withErrors('You must be logged in.');
        }

        // Fetch Inventory Manager details
        $userSQL = DB::table('user')
            ->select('user.*')
            ->where('role', '=', 'Inventory Manager')
            ->get();

        // Get the selected category IDs
        $categoryIds = $request->get('category_ids', []);

        // If no categories are selected, show all products
        $inventoryJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->orderBy('product.product_name', 'asc');

        if (!empty($categoryIds)) {
            $inventoryJoined = $inventoryJoined->whereIn('category.category_id', $categoryIds);
        }

        $inventoryJoined = $inventoryJoined->distinct()->get();

        // Filter unique products
        $inventoryJoined = $inventoryJoined->unique('product_id');

        // Decode the description for each inventory item
        foreach ($inventoryJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        // Fetch all categories for filtering or display purposes
        $categories = Category::all();

        // Fetch all categories for filtering or display purposes
        $suppliers = Supplier::all();

        return view('inventory.inventory_table', [
            'userSQL' => $userSQL,
            'inventoryJoined' => $inventoryJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }



    public function supplierFilter(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->withErrors('You must be logged in.');
        }

        // Fetch Inventory Manager details
        $userSQL = DB::table('user')
            ->select('user.*')
            ->where('role', '=', 'Inventory Manager')
            ->get();

        // Get the selected category IDs
        $supplierIds = $request->get('supplier_ids', []);

        // If no categories are selected, show all products
        $inventoryJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->orderBy('product.product_name', 'asc');

        if (!empty($supplierIds)) {
            $inventoryJoined = $inventoryJoined->whereIn('supplier.supplier_id', $supplierIds);
        }

        $inventoryJoined = $inventoryJoined->distinct()->get();

        // Filter unique products
        $inventoryJoined = $inventoryJoined->unique('product_id');

        // Decode the description for each inventory item
        foreach ($inventoryJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        // Fetch all categories for filtering or display purposes
        $categories = Category::all();

        // Fetch all categories for filtering or display purposes
        $suppliers = Supplier::all();

        return view('inventory.inventory_table', [
            'userSQL' => $userSQL,
            'inventoryJoined' => $inventoryJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }
}
