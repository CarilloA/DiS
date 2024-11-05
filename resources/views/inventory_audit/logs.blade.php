@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

<style>
    body {
        background-image: url('/storage/images/bg-photo.jpeg');
        background-size: cover; /* Cover the entire viewport */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat; /* Prevent the image from repeating */
    }

    /* Main content styling */
    .main-content {
        padding: 20px; /* Add padding for inner spacing */
        margin: 0 20px; /* Add left and right margin */
        color: #fff !important;
        background-color: #565656 !important; 
        border-radius: 5px; /* Slightly rounded corners */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }

    h1.h2 {
        color: #fff; /* Change this to your desired color */
    }

    .table th, td {
        background-color: #565656 !important; /* Set background color for all table headers */
        color: #ffffff !important;
    }

    .table th, td {
        background-color: #f8f9fa !important; /* Set background color for all table headers */
    }
    
    /*Icon*/
    .circle-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px; /* Set the width of the circle */
        height: 30px; /* Set the height of the circle */
        border-radius: 50%; /* Makes it a circle */
        background-color: #dc3545; /* Light red background */
        color: white; /* Icon color */
        font-size: 1.5rem; /* Adjust icon size */
        transition: background-color 0.3s; /* Smooth transition for background color */
    }

    .circle-icon:hover {
        background-color: #c82333; /* Darker red on hover */
    }

    /*Date Picker*/
    .input-group .form-control {
        background-color: #212529; /* Grey background for input */
        color: white; /* White text */
        border-radius: 5px; /* Rounded corners */
        border: none;
    }

    .input-group .form-control:focus {
        background-color: #212529; /* Maintain grey background on focus */
        color: white; /* White text */
        outline: none; /* Remove default outline */
    }

    .input-group .input-group-text {
        background-color: #198754; /* Background for 'to' text */
        color: white; /* Text color */
        border-radius: 5px; /* Rounded corners */
        border: none;
    }

    .btn-success {
        background-color: #28a745; /* Button color */
        border-color: #28a745; /* Border color */
    }

    .table th, td {
        background-color: #565656 !important; /* Set background color for all table headers */
        color: #ffffff !important;
    }

    .modal-content {
        color: black !important; /* This will apply to all text in the modal */
        margin: 20px 15px;
    }

    .modal-header, .modal-footer {
        margin-bottom: 15px; /* Space between header/footer and body */
    }

    .modal-body {
        margin-top: 10px; /* Space above the body content */
    }

    /* Optional: For better spacing around specific elements */
    .form-group {
        margin-bottom: 1rem; /* Space below each form group */
    }

    
</style>    

@section('content')
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                    <h1 class="h2 mb-4">Audit Logs</h1>
                </div>
                    <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Auditor</th>
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
                                    <td>{{ $log->inventory->product->product_name }}</td>
                                    <td>{{ $log->user->username }}</td>
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
            <!-- Alert Messages -->
            @include('common.alert')
            
        </main>
    </div>
@endsection
