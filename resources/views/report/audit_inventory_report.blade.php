@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
<style>
    @media print {
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
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary ms-2 me-2 printButton" onclick="window.print();">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>
            <table class="table table-bordered">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                    <h5>Product Details</h5> 
                    <h5><strong>Report Period:</strong> {{ $startDate }} to {{ $endDate }}</h5>
                </div>
                <thead>
                    <tr>
                        <th>Auditor</th>
                        <th>Product No.</th>
                        <th>Product Name</th>
                        <th>Previous QoH</th>
                        <th>New Store Stock</th>
                        <th>New Stockroom Stock</th>
                        <th>New QoH</th>
                        <th>Variance</th>
                        <th>Reason</th>
                        <th>Audit Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($auditLogs as $log)
                    <div>
                        <tr>
                            <td>{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                            <td>{{ $log->inventory->product->product_id }}</td>
                            <td>{{ $log->inventory->product->product_name }}</td>
                            <td>{{ $log->previous_quantity_on_hand }}</td>
                            <td>{{ $log->new_store_quantity }}</td>
                            <td>{{ $log->new_stockroom_quantity }}</td>
                            <td>{{ $log->new_quantity_on_hand }}</td>
                            <td>{{ $log->variance }}</td>
                            <td>{{ $log->reason }}</td>
                            <td>{{ $log->audit_date }}</td>
                        </tr>
                    </div>
                @endforeach
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection
