<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InventoryAudit;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        // ->join('credentials', 'user.credential_id', '=', 'credentials.credential_id')
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

    public function update(Request $request, $inventory_id) {
        // Validate the incoming audit data
        $request->validate([
            'new_store_quantity' => 'required|integer|min:0',
            'new_stockroom_quantity' => 'required|integer|min:0',
            'new_quantity_on_hand' => 'required|integer|min:0',
            'variance' => 'required|integer',
            'reason' => 'required|string|max:30',
            'confirm_username' => 'required|string',
            'confirm_password' => 'required|string',
        ]);

        // Verify username and password for audit
        $user = Auth::user();
        if (!Hash::check($request->confirm_password, $user->password) || $user->username !== $request->confirm_username) {
            return back()->withErrors(['confirm_password' => 'Invalid username or password'])->withInput(); //can be use for modal errors
        }

        // Update inventory record with new audit data
        DB::table('inventory')
            ->where('inventory_id', $inventory_id)
            ->update([
                'in_stock' => $request->new_quantity_on_hand,
                'updated_at' => now(),
            ]);

        DB::table('stockroom')
            ->where('stockroom_id', $request->stockroom_id)
            ->update([
                'product_quantity' => $request->new_stockroom_quantity,
            ]);

        // Save audit details in an audit log if needed
        DB::table('inventory_audit')->insert([
            'audit_id' => $this->generateId('inventory_audit', 'audit_id'),
            'inventory_id' => $inventory_id,
            'previous_quantity_on_hand' => $request->previous_quantity_on_hand,
            'new_quantity_on_hand' => $request->new_quantity_on_hand,
            'new_stockroom_quantity' => $request->new_stockroom_quantity,
            'new_store_quantity' => $request->new_store_quantity,
            'variance' => $request->variance,
            'reason' => $request->reason,
            'user_id' => Auth::user()->user_id,
            'audit_date' => now(),
        ]);

    return redirect()->route('audit_inventory_table')->with('success', 'Inventory audited successfully.');
    }

    public function logs() {
        $auditLogs = InventoryAudit::with(['inventory', 'user'])->orderBy('audit_date', 'desc')->get();
        return view('inventory_audit.logs', compact('auditLogs'));
    }
}
