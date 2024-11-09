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
            ->join('stockroom', 'stock_transfer.to_stockroom_id', '=', 'stockroom.stockroom_id')
            ->join('category', 'product.category_id', '=', 'category.category_id')
            ->join('supplier', 'product.supplier_id', '=', 'supplier.supplier_id')
            ->select('inventory.*', 'product.*', 'category.*', 'supplier.*', 'stock_transfer.*', 'stockroom.*')
            ->whereBetween(DB::raw('DATE(updated_at)'), [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Decode description to array
        foreach ($inventoryJoined as $item) {
            $item->descriptionArray = json_decode($item->description, true);
        }

        // Return the view with the report data
        return view('report.inventory_report', compact('inventoryJoined', 'startDate', 'endDate'));
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

        // Return the view with the report data
        return view('report.audit_inventory_report', compact('auditLogs', 'startDate', 'endDate'));
    }
}
