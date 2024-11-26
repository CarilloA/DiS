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
        }

        .table {
            width: 100%;
            font-size: 8pt;
        }

        .table th, .table td {
            padding: 4px;
            text-align: center;
        }

        .table th {
            background-color: #f8f9fa;
        }

        /* Make sure the table fits within the page */
        .table-bordered {
            border-collapse: collapse;
            page-break-inside: auto;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }

        .container-fluid,
        .main-content {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        #main-content {
            background-color: transparent !important;
            color: black !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Adjust header and footer for print */
        .main-content h1 {
            font-size: 18pt;
            text-align: center;
            margin-bottom: 10px;
        }
        .main-content h5 {
            font-size: 10pt;
            margin-bottom: 10px;
        }

        .printButton {
            display: none;
        }

        /* Ensure the content fits within the bond paper size */
        /* @page {
            size: 8.5in 11in;
            margin: 0.5in;
        } */
    }

    .container-fluid,
    .main-content {
        margin-top: 0;
        padding-top: 0;
    }
    #main-content {
        padding: 20px; /* Add padding for inner spacing */
        margin: 0 20px; /* Add left and right margin */
        color: #fff;
        background-color: #565656; 
        border-radius: 5px; /* Slightly rounded corners */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }

    body {
        background-image: url('/storage/images/bg-photo.jpeg');
        background-size: cover; /* Cover the entire viewport */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat; /* Prevent the image from repeating */
    }
    table th, td {
        text-align: center;
    }

    .printButton {
        margin-bottom: 1em;
    }

</style>
<div class="container-fluid">
    <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="main-content" id="main-content">
            <!-- Alert Messages -->
            @include('common.alert')
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1>Audit Inventory Report</h1>
            </div>
            <table class="table table-bordered">
                <div class="d-flex justify-content-start">
                    <?php 
                        $user = auth()->user();
                    ?>
                    <h5><strong>Reporter:</strong> {{ $user->first_name }} {{ $user->last_name }}</h5>
                </div>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                    {{-- <h5>Audit Logs</h5>  --}}
                    <h5><strong>Report Period:</strong> {{ $startDate }} to {{ $endDate }}</h5>
                    <button type="button" class="btn btn-primary ms-2 me-2 printButton" onclick="window.print();">
                        <i class="fa-solid fa-print"></i> Print Report
                    </button>
                </div>
                <thead>
                    <tr>
                        <th>Audit No.</th>
                        <th>Auditor</th>
                        <th>Product Name</th>
                        <th>previous Store Stock</th>
                        <th>Previous Stockroom Stock</th>
                        <th>Previous QoH</th>
                        <th>New Store Stock</th>
                        <th>New Stockroom Stock</th>
                        <th>New QoH</th>
                        <th>Store Stock Discrepancy</th>
                        <th>Stockroom Stock Discrepancy	</th>
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
                            <td>{{ $log->audit_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center">No data available for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="3">Audit No.</th>
                        <th colspan="3">Location</th>
                    </tr>
                    <tr>
                        <th rowspan="2">Store</th>
                        <th colspan="2">Stockroom</th>
                    </tr>
                    <tr>
                        <th>Aisle No.</th>
                        <th>Cabinet Level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditLogs as $log)
                        <tr>
                            <td>{{ $log->audit_id }}</td>
                            <td>{{ $log->store_stock_discrepancy ? 'In-Store' : 'N/A' }}</td>
                            <td>{{ $log->stockroom_stock_discrepancy ? $inventoryJoined->firstWhere('inventory_id', $log->inventory_id)->aisle_number ?? 'N/A' : 'N/A' }}</td>
                            <td>{{ $log->stockroom_stock_discrepancy ? $inventoryJoined->firstWhere('inventory_id', $log->inventory_id)->cabinet_level ?? 'N/A' : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No data available for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Audit No.</th>
                        <th>Steps taken to resolve discrepancies</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditLogs as $log)
                        <tr>
                            <td>{{ $log->audit_id }}</td>
                            <td style="white-space: pre-wrap;">{{ $log->resolve_steps }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No data available for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection
