<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Stockroom;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            // Prioritize products that need restocking (either store or stockroom)
            ->orderByRaw('
            CASE 
                WHEN inventory.in_stock - stockroom.product_quantity <= inventory.reorder_level THEN 1 
                WHEN stockroom.product_quantity <= inventory.reorder_level THEN 2 
                ELSE 3 
            END, updated_at DESC')
            ->get();

        // Filter duplicates based on a unique key (e.g., product_id)
        $productJoined = $productJoined->unique('product_id');

        // Decode the description for each inventory item
        foreach ($productJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true); // Decode the JSON description into an array
        }

        // low stocks
        $lowStoreStockMessages = [];
        $lowStockroomStockMessages = [];
        $processedProducts = [];  // Array to track products that have been processed

        // stockroom restock
        foreach ($productJoined as $data) {
            $restockStore = $data->in_stock - $data->product_quantity;
        
            // Check if the product is low on stock for either the store or the stockroom
            if (!in_array($data->product_id, $processedProducts)) {
                if ($restockStore <= $data->reorder_level) {
                    $lowStoreStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the store.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
        
                if ($data->product_quantity <= $data->reorder_level) {
                    $lowStockroomStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the stockroom.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
            }
        }    
            
        // Pass the counts to the view
        $lowStoreStockCount = count($lowStoreStockMessages);
        $lowStockroomStockCount = count($lowStockroomStockMessages);

        // Fetch all categories for filtering or display purposes
        $categories = Category::all();

        // Fetch all categories for filtering or display purposes
        $suppliers = Supplier::all();

        // Pass the inventory managers and user role to the view
        return view('purchase.purchase_table', [
            'userSQL' => $userSQL,
            'productJoined' => $productJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'lowStoreStockCount' => $lowStoreStockCount,
            'lowStockroomStockCount' => $lowStockroomStockCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::all(); // Fetch all suppliers
        $categories = Category::all(); // Fetch all categories
        return view('purchase.create_product', compact('suppliers', 'categories')); // Pass to the view
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

    public function getSupplierDetails(Request $request)
    {
        $supplier = Supplier::find($request->supplier_id);
        return response()->json($supplier);
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
            'image_url' => ['image'],
            'product_name' => ['required', 'string', 'max:30'],
            'category_dropdown' => ['required'],
            'category_name' => ['nullable', 'string', 'max:30', 'unique:category,category_name',],
            'purchase_price_per_unit' => ['required', 'numeric'],
            'sale_price_per_unit' => ['required', 'numeric'],
            'unit_of_measure' => ['required', 'string', 'max:15'],
            'in_stock' => ['required', 'numeric'],
            'reorder_level' => ['required', 'numeric'],
            'color' => ['max:50'],
            'size' => ['max:50'],
            'description' => ['max:255'],
            'supplier_dropdown' => ['required'],
            'company_name' => ['nullable', 'string', 'max:30'],
            'contact_person' => ['nullable', 'string', 'max:30'],
            'mobile_number' => ['nullable', 'numeric'],
            'email' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:50'],
            'aisle_number' => ['numeric'],
            'cabinet_level' => ['numeric'],
            'product_quantity' => ['numeric'],
        ]);

         // Handle file upload with a default image if no file is provided
         $fileNameToStore = 'noimage.jpg'; 
         if ($request->hasFile('image_url')) {
             $fileNameToStore = $this->handleFileUpload($request->file('image_url'));
         }

        // Use a transaction to ensure data integrity
        DB::transaction(function () use ($validatedData, $fileNameToStore ) {
            // Handle category logic
            $categoryId = $validatedData['category_dropdown'];
            if ($categoryId === 'add-new-category') {
                // Create a new Category
                $category = Category::create([
                    'category_id' => $this->generateId('category'), // Generate custom ID for category
                    'category_name' => $validatedData['category_name'],
                ]);
                $categoryId = $category->category_id; // Get the new supplier's ID
            }

             // Handle supplier logic
            $supplierId = $validatedData['supplier_dropdown'];
            if ($supplierId === 'add-new') {
                // Create a new supplier
                $supplier = Supplier::create([
                    'supplier_id' => $this->generateId('supplier'), // Generate custom ID for supplier
                    'company_name' => $validatedData['company_name'],
                    'contact_person' => $validatedData['contact_person'],
                    'mobile_number' => $validatedData['mobile_number'],
                    'email' => $validatedData['email'],
                    'address' => $validatedData['address'],
                ]);
                $supplierId = $supplier->supplier_id; // Get the new supplier's ID
            }

            // Create the Product
            $product = Product::create([
                'image_url' => $fileNameToStore,
                'product_id' => $this->generateId('product'), // Generate custom ID for product
                'product_name' => $validatedData['product_name'],
                'description' => json_encode([ // Encode the array as JSON
                    'color' => $validatedData['color'],
                    'size' => $validatedData['size'],
                    'description' => $validatedData['description'],
                ]),
                'category_id' => $categoryId, // Use the existing or newly created category ID
                'supplier_id' => $supplierId, // Use the existing or newly created supplier ID
            ]);

            // Create the Stockroom
            $stockroom = Stockroom::create([
                'stockroom_id' => $this->generateId('stockroom'), // Generate custom ID for product
                'aisle_number' => $validatedData['aisle_number'],
                'cabinet_level' => $validatedData['cabinet_level'],
                'product_quantity' => $validatedData['product_quantity'],
                'category_id' => $categoryId, // Use the existing or newly created category ID
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

    private function handleFileUpload($file)
    {
        $fileNameWithExt = $file->getClientOriginalName();
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
        $file->storeAs('public/userImage', $fileNameToStore);

        return $fileNameToStore;
    }


    public function restock(Request $request) 
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'product_id' => ['required', 'exists:product,product_id'],
            'purchase_price_per_unit' => ['required', 'numeric'],
            'sale_price_per_unit' => ['required', 'numeric'],
            'unit_of_measure' => ['required', 'string', 'max:15'],
            'previous_quantity' => ['required', 'numeric', 'min:1'],
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
                'updated_at' => now(),
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
                'product_quantity' => $validatedData['previous_quantity'] + $validatedData['quantity'],
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

    // filter
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
    
        $productJoined = $inventoryQuery->get();
        $productJoined = $productJoined->unique('product_id');
    
        // Decode description for each inventory item
        foreach ($productJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        // low stocks
        $lowStoreStockMessages = [];
        $lowStockroomStockMessages = [];
        $processedProducts = [];  // Array to track products that have been processed

        // stockroom restock
        foreach ($productJoined as $data) {
            $restockStore = $data->in_stock - $data->product_quantity;
        
            // Check if the product is low on stock for either the store or the stockroom
            if (!in_array($data->product_id, $processedProducts)) {
                if ($restockStore <= $data->reorder_level) {
                    $lowStoreStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the store.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
        
                if ($data->product_quantity <= $data->reorder_level) {
                    $lowStockroomStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the stockroom.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
            }
        }    
            
        // Pass the counts to the view
        $lowStoreStockCount = count($lowStoreStockMessages);
        $lowStockroomStockCount = count($lowStockroomStockMessages);
    
        // Fetch categories and suppliers for filtering or display purposes
        $categories = Category::all();
        $suppliers = Supplier::all();
    
        return view('purchase.purchase_table', [
            'userSQL' => $userSQL,
            'productJoined' => $productJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'lowStoreStockCount' => $lowStoreStockCount,
            'lowStockroomStockCount' => $lowStockroomStockCount,
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
        $productJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->orderBy('product.product_name', 'asc');

        if (!empty($categoryIds)) {
            $productJoined = $productJoined->whereIn('category.category_id', $categoryIds);
        }

        $productJoined = $productJoined->distinct()->get();

        // Filter unique products
        $productJoined = $productJoined->unique('product_id');

        // Decode the description for each inventory item
        foreach ($productJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        // low stocks
        $lowStoreStockMessages = [];
        $lowStockroomStockMessages = [];
        $processedProducts = [];  // Array to track products that have been processed

        // stockroom restock
        foreach ($productJoined as $data) {
            $restockStore = $data->in_stock - $data->product_quantity;
        
            // Check if the product is low on stock for either the store or the stockroom
            if (!in_array($data->product_id, $processedProducts)) {
                if ($restockStore <= $data->reorder_level) {
                    $lowStoreStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the store.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
        
                if ($data->product_quantity <= $data->reorder_level) {
                    $lowStockroomStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the stockroom.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
            }
        }    
            
        // Pass the counts to the view
        $lowStoreStockCount = count($lowStoreStockMessages);
        $lowStockroomStockCount = count($lowStockroomStockMessages);

        // Fetch all for filtering or display purposes
        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('purchase.purchase_table', [
            'userSQL' => $userSQL,
            'productJoined' => $productJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'lowStoreStockCount' => $lowStoreStockCount,
            'lowStockroomStockCount' => $lowStockroomStockCount,
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
        $productJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->orderBy('product.product_name', 'asc');

        if (!empty($supplierIds)) {
            $productJoined = $productJoined->whereIn('supplier.supplier_id', $supplierIds);
        }

        $productJoined = $productJoined->distinct()->get();

        // Filter unique products
        $productJoined = $productJoined->unique('product_id');

        // Decode the description for each inventory item
        foreach ($productJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        // low stocks
        $lowStoreStockMessages = [];
        $lowStockroomStockMessages = [];
        $processedProducts = [];  // Array to track products that have been processed

        // stockroom restock
        foreach ($productJoined as $data) {
            $restockStore = $data->in_stock - $data->product_quantity;

            // Check if the product is low on stock for either the store or the stockroom
            if (!in_array($data->product_id, $processedProducts)) {
                if ($restockStore <= $data->reorder_level) {
                    $lowStoreStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the store.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }

                if ($data->product_quantity <= $data->reorder_level) {
                    $lowStockroomStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the stockroom.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
            }
        }    
            
        // Pass the counts to the view
        $lowStoreStockCount = count($lowStoreStockMessages);
        $lowStockroomStockCount = count($lowStockroomStockMessages);

        // Fetch all for filtering or display purposes
        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('purchase.purchase_table', [
            'userSQL' => $userSQL,
            'productJoined' => $productJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'lowStoreStockCount' => $lowStoreStockCount,
            'lowStockroomStockCount' => $lowStockroomStockCount,
        ]);
    }
    
    // store restock filter
    public function storeRestockFilter()
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

        $productJoined = Inventory::with('product')
        ->join('product', 'inventory.product_id', '=', 'product.product_id')
        ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
        ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
        ->select('inventory.*', 'product.*', 'stock_transfer.*', 'stockroom.*')
        ->where(DB::raw('inventory.in_stock - stockroom.product_quantity'), '<=', DB::raw('reorder_level'))  // For store products that need restocking
        ->get();

        // Filter duplicates based on a unique key (e.g., product_id)
        $productJoined = $productJoined->unique('product_id');

        // Decode the description for each inventory item
        foreach ($productJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true); // Decode the JSON description into an array
        }

        // low stocks
        $lowStoreStockMessages = [];
        $lowStockroomStockMessages = [];
        $processedProducts = [];  // Array to track products that have been processed

        // stockroom restock
        foreach ($productJoined as $data) {
            $restockStore = $data->in_stock - $data->product_quantity;
        
            // Check if the product is low on stock for either the store or the stockroom
            if (!in_array($data->product_id, $processedProducts)) {
                if ($restockStore <= $data->reorder_level) {
                    $lowStoreStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the store.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
        
                if ($data->product_quantity <= $data->reorder_level) {
                    $lowStockroomStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the stockroom.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
            }
        }    
            
        // Pass the counts to the view
        $lowStoreStockCount = count($lowStoreStockMessages);
        $lowStockroomStockCount = count($lowStockroomStockMessages);

        // Fetch all categories for filtering or display purposes
        $categories = Category::all();

        // Fetch all categories for filtering or display purposes
        $suppliers = Supplier::all();

        // Pass the inventory managers and user role to the view
        return view('purchase.purchase_table', [
            'userSQL' => $userSQL,
            'productJoined' => $productJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'lowStoreStockCount' => $lowStoreStockCount,
            'lowStockroomStockCount' => $lowStockroomStockCount,
        ]);
    }

    // stockroom restock filter
    public function stockroomRestockFilter()
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

        $productJoined = Inventory::with('product')
        ->join('product', 'inventory.product_id', '=', 'product.product_id')
        ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
        ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
        ->select('inventory.*', 'product.*', 'stock_transfer.*', 'stockroom.*')
        ->where('stockroom.product_quantity', '<=', DB::raw('reorder_level')) // For stockroom products that need restocking
        ->get();

        // Filter duplicates based on a unique key (e.g., product_id)
        $productJoined = $productJoined->unique('product_id');

        // Decode the description for each inventory item
        foreach ($productJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true); // Decode the JSON description into an array
        }

        // low stocks
        $lowStoreStockMessages = [];
        $lowStockroomStockMessages = [];
        $processedProducts = [];  // Array to track products that have been processed

        // stockroom restock
        foreach ($productJoined as $data) {
            $restockStore = $data->in_stock - $data->product_quantity;
        
            // Check if the product is low on stock for either the store or the stockroom
            if (!in_array($data->product_id, $processedProducts)) {
                if ($restockStore <= $data->reorder_level) {
                    $lowStoreStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the store.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
        
                if ($data->product_quantity <= $data->reorder_level) {
                    $lowStockroomStockMessages[] = "Product ID {$data->product_id} ({$data->product_name}) is low on stock. Please restock the stockroom.";
                    $processedProducts[] = $data->product_id; // Mark as processed
                }
            }
        }    
            
        // Pass the counts to the view
        $lowStoreStockCount = count($lowStoreStockMessages);
        $lowStockroomStockCount = count($lowStockroomStockMessages);

        // Fetch all categories for filtering or display purposes
        $categories = Category::all();

        // Fetch all categories for filtering or display purposes
        $suppliers = Supplier::all();

        // Pass the inventory managers and user role to the view
        return view('purchase.purchase_table', [
            'userSQL' => $userSQL,
            'productJoined' => $productJoined,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'lowStoreStockCount' => $lowStoreStockCount,
            'lowStockroomStockCount' => $lowStockroomStockCount,
        ]);
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
    public function edit($productId)
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
        ->where('product.product_id', '=', $productId)
        ->first();

    // Decode the description for the product
// Decode the description for the product if it's set
$descriptionArray = [];
if ($productJoined && isset($productJoined->description)) {
    $descriptionArray = json_decode($productJoined->description, true); // Decode the JSON description into an array
}

    // Fetch all categories for filtering or display purposes
    $categories = Category::all();
    $suppliers = Supplier::all();

    return view('purchase.update_product', compact('userSQL', 'productJoined', 'categories', 'suppliers', 'descriptionArray'));
}


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $productId)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'image_url' => ['image', 'nullable'],
        'product_name' => ['required', 'string', 'max:30'],
        'category_dropdown' => ['required'],
        'category_name' => ['nullable', 'string', 'max:30', 'unique:category,category_name',],
        'purchase_price_per_unit' => ['required', 'numeric'],
        'sale_price_per_unit' => ['required', 'numeric'],
        'unit_of_measure' => ['required', 'string', 'max:15'],
        'in_stock' => ['required', 'numeric'],
        'reorder_level' => ['required', 'numeric'],
        'color' => ['max:50'],
        'size' => ['max:50'],
        'description' => ['max:255'],
        'supplier_dropdown' => ['required'],
        'company_name' => ['nullable', 'string', 'max:30'],
        'contact_person' => ['nullable', 'string', 'max:30'],
        'mobile_number' => ['nullable', 'numeric'],
        'email' => ['nullable', 'string', 'max:30'],
        'address' => ['nullable', 'string', 'max:50'],
        'aisle_number' => ['numeric'],
        'cabinet_level' => ['numeric'],
        'product_quantity' => ['numeric'],
    ]);

    // Find the product by its ID
    $product = Product::findOrFail($productId);
    $fileNameToStore = $product->image_url; // Default to the existing image

    // Handle file upload if a new image is provided
    if ($request->hasFile('image_url')) {
        $fileNameToStore = $this->handleFileUpload($request->file('image_url'));
    }

    // Use a transaction to ensure data integrity
    DB::transaction(function () use ($validatedData, $fileNameToStore, $product) {
        // Handle category logic
        $categoryId = $validatedData['category_dropdown'];
        if ($categoryId === 'add-new-category') {
            // Create a new Category
            $category = Category::create([
                'category_id' => $this->generateId('category'), // Generate custom ID for category
                'category_name' => $validatedData['category_name'],
            ]);
            $categoryId = $category->category_id; // Get the new category's ID
        }

        // Handle supplier logic
        $supplierId = $validatedData['supplier_dropdown'];
        if ($supplierId === 'add-new') {
            // Create a new supplier
            $supplier = Supplier::create([
                'supplier_id' => $this->generateId('supplier'), // Generate custom ID for supplier
                'company_name' => $validatedData['company_name'],
                'contact_person' => $validatedData['contact_person'],
                'mobile_number' => $validatedData['mobile_number'],
                'email' => $validatedData['email'],
                'address' => $validatedData['address'],
            ]);
            $supplierId = $supplier->supplier_id; // Get the new supplier's ID
        }

        // Update the Product
        $product->update([
            'image_url' => $fileNameToStore,
            'product_name' => $validatedData['product_name'],
            'description' => json_encode([ // Encode the array as JSON
                'color' => $validatedData['color'],
                'size' => $validatedData['size'],
                'description' => $validatedData['description'],
            ]),
            'category_id' => $categoryId, // Use the existing or newly created category ID
            'supplier_id' => $supplierId, // Use the existing or newly created supplier ID
        ]);

        $productJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->where('product.product_id', '=',  $product->product_id)
            ->first();

        // Retrieve the stockroom using the joined result, assuming the stockroom ID is available
        $stockroom = Stockroom::where('stockroom_id', $productJoined->stockroom_id)->firstOrFail();

        $stockroom->update([
            'aisle_number' => $validatedData['aisle_number'],
            'cabinet_level' => $validatedData['cabinet_level'],
            'product_quantity' => $validatedData['product_quantity'],
            'category_id' => $categoryId, // Use the existing or newly created category ID
        ]);

        // Update the StockTransfer if necessary
        $stockTransfer = StockTransfer::where('product_id', $product->product_id)->firstOrFail();
        $stockTransfer->update([
            'transfer_quantity' => $validatedData['product_quantity'],
            'transfer_date' => now(),
            'to_stockroom_id' => $stockroom->stockroom_id, // Use the generated stockroom_id
        ]);

        // Update the Inventory
        $inventory = Inventory::where('product_id', $product->product_id)->firstOrFail();
        $inventory->update([
            'purchase_price_per_unit' => $validatedData['purchase_price_per_unit'],
            'sale_price_per_unit' => $validatedData['sale_price_per_unit'],
            'unit_of_measure' => $validatedData['unit_of_measure'],
            'in_stock' => $validatedData['in_stock'],
            'reorder_level' => $validatedData['reorder_level'],
        ]);
    });

    // Redirect or return response after successful update
    return redirect()->route('purchase_table')->with('success', 'Product updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
{
    // Validate the provided password
    $validatedData = $request->validate([
        'password' => 'required|string',
    ]);

    // Check if the current password matches
    if (!Hash::check($validatedData['password'], Auth::user()->password)) {
        // If password is incorrect, redirect back with error
        return back()->with([
            'delete_error' => 'The password you entered is incorrect.',
            'error_product_id' => $id, // Pass the product ID with an error
        ]);
    }

    // Find and delete the product
    $product = Product::find($id);

    if ($product) {
        $product->delete();
        // Redirect with success message
        return redirect()->route('purchase_table')->with('success', 'Product deleted successfully.');
    }

    // If product not found
    return back()->withErrors(['error' => 'Product not found.']);
}
}
