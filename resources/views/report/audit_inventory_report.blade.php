@extends('layouts.app')

<!-- Wrap navbar in a div with a specific class for print -->
<div class="navbar-print-hide">
    @include('common.navbar')
</div>

@section('content')
<style>
    @media print {
    .navbar-print-hide {
        display: none; /* Hide the navbar during printing */
    }

    body {
        background-image: none; /* Remove background image in print */
        font-size: 10pt; /* Ensure font size is appropriate for the report */
        margin: 0;
        padding: 0;
        line-height: 1.6; /* Improve line spacing for readability */
    }

    .table {
        width: 100%;
        font-size: 9pt;
        border-collapse: collapse;
    }

    .table th, .table td {
        padding: 6px;
        text-align: left;
        border: 1px solid #dee2e6;
    }

    .table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    /* Container for the main content */
    .container-fluid {
        max-width: 100%;
        margin: 0;
        padding: 0;
    }

    #main-content {
        background-color: transparent !important;
        color: #000 !important;
        box-shadow: none !important;
        padding: 40px 30px;
        margin: 0;
    }

    /* Report Header Styling */
    .report-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .report-header h1 {
        font-size: 26px;
        font-weight: bold;
        color: #000;
        margin-bottom: 15px;
    }

    .report-header h5 {
        font-size: 14pt;
        font-style: italic;
        color: #333;
        margin-top: 10px;
    }

    /* Hide Print Button in print view */
    .printButton {
        display: none; /* Hide the print button during printing */
    }

    /* Footer styling for Prepared By section */
    .prepared-by {
        text-align: center;
        font-size: 12pt;
        font-style: italic;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .prepared-by h5 {
        font-weight: normal;
        color: #333;
    }

    /* Ensure the content fits within the bond paper size */
    /* @page {
        size: 8.5in 11in;
        margin: 1in;
    } */
}


    /* Styling for report title and sections in normal view */
    .container-fluid,
    .main-content {
        margin-top: 0;
        padding-top: 0;
    }

    #main-content {
        background-color: #fff; /* White background for normal view */
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .table td,
    .table th {
        text-align: left; /* Align text to the left for better readability */
    }

    .table {
        margin-top: 30px;
        width: 100%;
        border: 1px solid #dee2e6;
        font-size: 10pt;
        border-radius: 5px;
        overflow: hidden;
    }

    .table-bordered td,
    .table-bordered th {
        padding: 8px;
    }
</style>

<div class="container-fluid">
    <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="main-content" id="main-content">
            <!-- Alert Messages -->
            @include('common.alert')

            <!-- Report Header -->
            <div class="report-header">
                <h1>{{ $reportTitle }}</h1>
                <h5>Report generated on {{ \Carbon\Carbon::now()->format('F j, Y') }}</h5>
            </div>

            <!-- Print Button -->
            <div class="printButton">
                <button type="button" onclick="window.print();">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>

            <!-- Table for audit logs -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Audit No.</th>
                        <th>Auditor</th>
                        <th>Product Name</th>
                        <th>Previous Store Stock</th>
                        <th>Previous Stockroom Stock</th>
                        <th>Previous QoH</th>
                        <th>New Store Stock</th>
                        <th>New Stockroom Stock</th>
                        <th>New QoH</th>
                        <th>Store Stock Discrepancy</th>
                        <th>Stockroom Stock Discrepancy</th>
                        <th>QoH Discrepancy</th>
                        <th>Discrepancy Reason</th>
                        <th>Audit Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditLogs as $log)
                        <tr>
                            <td>{{ $log->audit_id }}</td>
                            <td>{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                            <td>{{ $log->inventory->product->product_name }}</td>
                            <td>{{ $log->previous_store_quantity }}</td>
                            <td>{{ $log->previous_stockroom_quantity }}</td>
                            <td>{{ $log->previous_quantity_on_hand }}</td>
                            <td>{{ $log->new_store_quantity }}</td>
                            <td>{{ $log->new_stockroom_quantity }}</td>
                            <td>{{ $log->new_quantity_on_hand }}</td>
                            <td>{{ $log->store_stock_discrepancy }}</td>
                            <td>{{ $log->stockroom_stock_discrepancy }}</td>
                            <td>{{ $log->in_stock_discrepancy }}</td>
                            <td>{{ $log->discrepancy_reason }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->audit_date)->format('F j, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center">No audit logs found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Footer: Prepared By -->
            <div class="prepared-by">
                <?php 
                    $user = auth()->user();
                ?>
                <h5><strong>Prepared By:</strong> {{ $user->first_name }} {{ $user->last_name }}</h5>
            </div>
        </div>
    </main>
</div>
@endsection
