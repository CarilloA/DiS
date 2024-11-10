<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InventoryAudit;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class InventoryAuditController extends Controller
{
    public function index() {
        // Check if the user is logged in
        if (!Auth::check()) {
            // If the user is not logged in, redirect to login
            return redirect('/login')->withErrors('You must be logged in.');
        }

        $user = Auth::user();

        $inventoryJoined = DB::table('inventory')
        ->join('product', 'inventory.product_id', '=', 'product.product_id')
        ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
        ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
        ->join('category', 'product.category_id', '=', 'category.category_id')
        ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
        ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
        ->orderBy('updated_at', 'desc')
        ->get();

        // Decode the description for each inventory item
        foreach ($inventoryJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true); // Decode the JSON description into an array
        }
        
        if ($user->role === "Auditor") {
            // Pass the inventory managers and user role to the view
            return view('inventory_audit.audit_inventory_table', [
                'inventoryJoined' => $inventoryJoined,
                'user' => $user,
            ]);
        }
    }

    private function generateId($table, $column = 'audit_id')
    {
        // Generate a random 8-digit number
        do {
            $id = random_int(10000000, 99999999);
        } while (DB::table($table)->where($column, $id)->exists()); // Ensure the ID is unique

        return $id;
    }

    public function showStep1() {
        // Fetch data from the inventory, join with related tables as needed
        $inventoryJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->get();
    
        return view('inventory_audit.step1', [
            'inventoryJoined' => $inventoryJoined,
            'progress' => 25 // Set progress for step 1
        ]);
    }

    // Controller code (InventoryAuditController.php)

    public function submitStep1AndGoToStep2(Request $request) {
        // Validate the submitted data
        $request->validate([
            'inventory_id' => 'required|array',
            'inventory_id.*' => 'required|integer',
            'count_quantity_on_hand' => 'required|array',
            'count_quantity_on_hand.*' => 'required|numeric',
            'count_store_quantity' => 'required|array',
            'count_store_quantity.*' => 'required|numeric',
            'count_stockroom_quantity' => 'required|array',
            'count_stockroom_quantity.*' => 'required|numeric',
        ]);
        
        // Retrieve the inventory data
        $inventoryJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->get();
        
        // Initialize the discrepancies array
        $discrepancies = [];
        
        // Calculate the variance and check for discrepancies
        foreach ($inventoryJoined as $key => $item) {
            // Calculate discrepancies for each inventory item
            $in_stock_discrepancy = $request->count_quantity_on_hand[$key] != $item->in_stock;
            $product_quantity_discrepancy = $request->count_stockroom_quantity[$key] != $item->product_quantity;
            
            // Calculate the store stock (in_stock - product_quantity)
            $store_stock = $item->in_stock - $item->product_quantity;
            $store_stock_discrepancy = $request->count_store_quantity[$key] != $store_stock;
            
            // If any discrepancy is found, store the data along with the variance
            if ($in_stock_discrepancy || $product_quantity_discrepancy || $store_stock_discrepancy) {
                // Calculate the variance (the difference in stock)
                $variance_in_stock = $request->count_quantity_on_hand[$key] - $item->in_stock;
                $variance_store_stock = $request->count_store_quantity[$key] - $store_stock;
                $variance_stockroom_quantity = $request->count_stockroom_quantity[$key] - $item->product_quantity;

                //fetch counted quantities of the products with discrepancy
                $fetch_quantity_on_hand = $request->count_quantity_on_hand[$key];
                $fetch_store_quantity = $request->count_store_quantity[$key];
                $fetch_stockroom_quantity = $request->count_stockroom_quantity[$key];

                //fetch inventory id of the products with discrepancy
                // $fetch_inventory_id = $request->inventory_id[$key];
                
                // Add the discrepancy with the variance information to the array
                $discrepancies[] = [
                    'inventory' => $item,
                    'variance_in_stock' => $variance_in_stock,
                    'variance_store_stock' => $variance_store_stock,
                    'variance_stockroom_quantity' => $variance_stockroom_quantity,
                    'fetch_quantity_on_hand' => $fetch_quantity_on_hand,
                    'fetch_store_quantity' => $fetch_store_quantity,
                    'fetch_stockroom_quantity' => $fetch_stockroom_quantity,
                    // 'fetch_inventory_id' => $fetch_inventory_id

                ];
            }
        }
        
        // Store discrepancies in the session
        session(['discrepancies' => $discrepancies]);
        
        // Redirect to step2
        return redirect()->route('step2');
    }
    
    public function showStep2() {
        // Retrieve discrepancies and input data from the session
        $discrepancies = session('discrepancies', []);
    
        return view('inventory_audit.step2', [
            'discrepancies' => $discrepancies,
            'progress' => 50
        ]);
    }
    
    public function submitStep2AndGoToStep3(Request $request) {
        // Validate the submitted data
        $request->validate([
            'inventory_id' => 'required|array',  // Ensure 'inventory_id' is an array
            'inventory_id.*' => 'required|integer',  // Validate that each inventory_id is an integer
            'reason' => 'required|array',  // Ensure 'reason' is an array
            'reason.*' => 'required|string',  // Validate that each reason is a string
        ]);
    
        // Initialize the audit data array
        $discrepancy_reason = [];
    
        // Collect and store audit data for each discrepancy
        foreach ($request->inventory_id as $key => $inventoryId) {
            $discrepancy_reason[] = [
                'inventory_id' => $inventoryId,  // Store the inventory_id
                'discrepancy_reason' => $request->reason[$key],  // Store the corresponding reason
            ];
        }
    
        // Store reason in the session
        session(['discrepancy_reason' => $discrepancy_reason]);
    
        // Redirect to step3
        return redirect()->route('step3');
    }
    

    public function showStep3() 
    {
        return view('inventory_audit.step3', [
            'progress' => 75 // Set progress for step 3
        ]);
    }

    //Update here the data
    public function submitStep3AndGoToStep4(Request $request) 
    {
        // Validate the submitted data
        $request->validate([
            'actions_taken' => 'required|string',
        ]);

        // Store actions_taken in the session
        session(['actions_taken' => $request->actions_taken]);

        // Redirect to step4
        return redirect()->route('step4');

    }

    public function showStep4() {

        // Retrieve data from the session
        $discrepancies = session('discrepancies', []);
    
        return view('inventory_audit.step4', [
            'discrepancies' => $discrepancies,
            'progress' => 100 // Set progress for step 4
        ]);
    }

    public function submitStep4(Request $request) {
        // Validate the adjustments
        $request->validate([
            'adjusted_quantity_on_hand.*' => 'required|numeric',
            'adjusted_store_quantity.*' => 'required|numeric',
            'adjusted_stockroom_quantity.*' => 'required|numeric',
            'confirm_username' => 'required|string',
            'confirm_password' => 'required|string',
            'confirm_admin_username' => 'required|string',
            'confirm_admin_password' => 'required|string',
        ]);

        // Retrieve data from the session
        $discrepancy_reason = session('discrepancy_reason', []);
        $discrepancies = session('discrepancies', []);
        $actions_taken = session('actions_taken', []);

        $user = Auth::user();

        // Auditor verification
        if (!Hash::check($request->confirm_password, $user->password) || $user->username !== $request->confirm_username) {
            return back()->withErrors(['confirm_password' => 'Invalid auditor credentials']);
        }

        // Admin verification
        $admin = DB::table('user')->where('role', 'Administrator')->first();
        if (!$admin || !Hash::check($request->confirm_admin_password, $admin->password) || $admin->username !== $request->confirm_admin_username) {
            return back()->withErrors(['confirm_admin_password' => 'Invalid admin credentials']);
        }
    
        // Process and update discrepancies in database
        foreach ($discrepancies as $key => $discrepancy) {
            $inventoryId = $discrepancy['inventory']->inventory_id;
            $in_stock = $discrepancy['inventory']->in_stock;
            $product_quantity = $discrepancy['inventory']->product_quantity;
            $adjustedQoH = $request->adjusted_quantity_on_hand[$key];
            $adjustedStoreQuantity = $request->adjusted_store_quantity[$key];
            $adjustedStockroomQuantity = $request->adjusted_stockroom_quantity[$key];
    
            // Update inventory with new values
            DB::table('inventory')->where('inventory_id', $inventoryId)->update([
                'in_stock' => $adjustedQoH,
                'updated_at' => now(),
            ]);

            DB::table('stockroom')->where('stockroom_id', $discrepancy['inventory']->stockroom_id)->update([
                'product_quantity' => $adjustedStockroomQuantity,
            ]);

            $store_stock = $in_stock - $product_quantity;

                // Save audit details in an audit log if needed
            DB::table('inventory_audit')->insert([
                'audit_id' => $this->generateId('inventory_audit', 'audit_id'),
                'inventory_id' => $inventoryId,
                'user_id' => $user->user_id,
                'previous_quantity_on_hand' => $in_stock,
                'previous_store_quantity' => $store_stock,
                'previous_stockroom_quantity' => $product_quantity,
                'new_quantity_on_hand' => $adjustedQoH,
                'new_stockroom_quantity' => $adjustedStockroomQuantity,
                'new_store_quantity' => $adjustedStoreQuantity,
                'in_stock_discrepancy' => $discrepancy['variance_in_stock'],
                'store_stock_discrepancy' => $discrepancy['variance_store_stock'],
                'stockroom_stock_discrepancy' => $discrepancy['variance_stockroom_quantity'],
                'discrepancy_reason' => $discrepancy_reason[$key]['discrepancy_reason'],
                'resolve_steps' => $actions_taken,
                'audit_date' => now(),
            ]);
        }
    
        return redirect()->route('inventory.audit.logs')->with('success', 'Inventory audited successfully.');
    }
    
    public function logs() {
        $auditLogs = InventoryAudit::with(['inventory', 'user'])->orderBy('audit_date', 'desc')->get();
        return view('inventory_audit.logs', compact('auditLogs'));
    }
}
