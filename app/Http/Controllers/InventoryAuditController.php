<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InventoryAudit;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryAuditController extends Controller
{
    public function index() {
        // Check if the user is logged in
        if (!Auth::check()) {
            // If the user is not logged in, redirect to login
            return redirect('/login')->withErrors('You must be logged in.');
        }

        $user = Auth::user();

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
        
        if ($user->role === "Auditor") {
            // Pass the inventory managers and user role to the view
            return view('inventory_audit.audit_inventory_table', [
                'inventoryJoined' => $inventoryJoined,
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

    public function update(Request $request, $inventory_id) {
        $request->validate([
            'new_quantity' => 'required|integer',
            'reason' => 'required|string|max:150',
        ]);
    
        $inventory = Inventory::findOrFail($inventory_id);
        $inventory->in_stock = $request->new_quantity;
        $inventory->save();
    
        // Log the change in InventoryAudit
        InventoryAudit::create([
            'audit_id' => $this->generateId('inventory_audit', 'audit_id'),
            'inventory_id' => $inventory_id,
            'user_id' => auth()->id(), // Assuming auditor is logged in
            'previous_quantity' => $request->previous_quantity,
            'new_quantity' => $request->new_quantity,
            'reason' => $request->reason,
            'audit_date' => now()
        ]);
    
        return redirect()->route('audit_inventory_table')->with('success', 'Inventory updated and audit log created.');
    }

    public function logs() {
        $auditLogs = InventoryAudit::with(['inventory', 'user'])->orderBy('audit_date', 'desc')->get();
        return view('inventory_audit.logs', compact('auditLogs'));
    }
}
