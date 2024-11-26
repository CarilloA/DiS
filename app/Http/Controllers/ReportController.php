<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryAudit;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the inventory report.
     */
    public function generateReport(Request $request)
    {
        // Validate the date range
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Query to get the inventory report data
        $inventoryJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('user', 'stock_transfer.user_id', '=', 'user.user_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'inventory.updated_at as inventory_updated_at', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*', 'user.*')
            ->whereBetween(DB::raw('DATE(inventory.updated_at)'), [$startDate, $endDate])
            ->orderBy('inventory.updated_at', 'desc')
            ->get();

        $stockTransferJoined = DB::table('stock_transfer')
        ->join('user', 'stock_transfer.user_id', '=', 'user.user_id')
        // ->join('sales_details', 'stock_transfer.product_id', '=', 'sales_details.product_id')
        ->select('stock_transfer.*', 'user.*')
        ->whereBetween(DB::raw('DATE(stock_transfer.transfer_date)'), [$startDate, $endDate])
        ->orderBy('transfer_date', 'desc')
        ->get();

        // Decode description to array
        foreach ($inventoryJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        // Return the view with the report data
        return view('report.inventory_report', compact('inventoryJoined', 'startDate', 'endDate', 'stockTransferJoined'));
    }
    /**
     * Display the audit inventory report.
     */
    public function generateAuditReport(Request $request)
    {
        // Validate the date range
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $auditLogs = InventoryAudit::with(['inventory', 'user'])
        ->whereBetween(DB::raw('DATE(audit_date)'), [$startDate, $endDate])
        ->orderBy('audit_date', 'desc')
        ->get();

        // Extract inventory IDs from the audit logs
        $inventoryIds = $auditLogs->pluck('inventory_id')->unique();

        // Query to get the inventory report data
        $inventoryJoined = DB::table('inventory')
            ->join('product', 'inventory.product_id', '=', 'product.product_id')
            ->join('stock_transfer', 'stock_transfer.product_id', '=', 'product.product_id')
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->whereIn('inventory.inventory_id', $inventoryIds)
            ->get();

        // Return the view with the report data
        return view('report.audit_inventory_report', compact('auditLogs', 'startDate', 'endDate', 'inventoryJoined'));
    }
}
