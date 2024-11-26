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

    .custom-date-picker {
        appearance: none; /* Removes the default appearance */
        -webkit-appearance: none; /* For Safari */
        position: relative;
        padding: 10px 40px 10px 10px; /* Adds padding to make room for the icon */
        background-color: #000; /* Ensures the input's background matches */
        color: #fff; /* White text color */
        border: 1px solid #fff; /* White border */
        border-radius: 5px;
        width: 28em;
        }

    /* This makes the original calendar icon invisible while keeping it clickable */
    .custom-date-picker::-webkit-calendar-picker-indicator {
        opacity: 0;
        display: block;
        position: absolute;
        right: 10px;
        width: 20px;
        height: 100%;
        cursor: pointer;
    }

    /* Custom white icon overlay */
    .custom-date-picker:before {
        content: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-1.99.9-1.99 2L3 20c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM7 12h5v5H7z"/></svg>');
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none; /* Makes the icon non-clickable but allows the input's functionality */
    }
</style>    

@section('content')
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="main-content">
                <!-- Alert Messages -->
                @include('common.alert')
                <div id="alertContainer"></div> <!-- Error message placeholder -->

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                    <h1 class="h2 mb-4">Audit Logs</h1>
                </div>
                <!-- Generate Report -->
                <form method="POST" action="{{ url('audit_inventory_report') }}" enctype="multipart/form-data" class="mb-4 report-form" id="reportForm">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="date" class="custom-date-picker" id="startDate" name="start_date" class="form-control" max="{{ date('Y-m-d') }}"  required>
                        <span class="input-group-text">TO</span>
                        <input type="date" class="custom-date-picker" id="endDate" name="end_date" class="form-control" max="{{ date('Y-m-d') }}"  required>
                        <button type="submit" class="btn btn-success ms-2">
                            <i class="fa-solid fa-print"></i> Generate Report
                        </button>
                    </div>
                </form>
                    <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Auditor</th>
                            <th>Product No.</th>
                            <th>Product Name</th>
                            <th>previous Store Stock</th>
                            <th>Previous Stockroom Stock</th>
                            <th>Previous QoH</th>
                            <th>New Store Stock</th>
                            <th>New Stockroom Stock</th>
                            <th>New QoH</th>
                            <th>Store Stock Discrepancy</th>
                            <th>Stockroom Stock Discrepancy	</th>
                            <th>in_stock_discrepancy</th>
                            <th>Discrepancy Reason</th>
                            <th>Resolve Steps</th>
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
                                    <td>
                                        <button type="button" class="btn" onclick="showResolveSteps('{{ htmlspecialchars($log->resolve_steps) }}')">
                                            <strong style="color: white; text-decoration: none; font-weight: normal;">more info.</strong>
                                        </button>
                                    </td>
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

<script>
    // error handling for generate report
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('reportForm');
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const alertContainer = document.getElementById('alertContainer');

        form.addEventListener('submit', function (event) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            // Clear previous errors
            alertContainer.innerHTML = '';

            if (startDate > endDate) {
                event.preventDefault(); // Prevent form submission

                // Create and append alert message
                const alertMessage = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> The end date cannot be earlier than the start date.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                alertContainer.innerHTML = alertMessage;
                return false;
            }
        });

        // Clear the alert container when input values are changed
        [startDateInput, endDateInput].forEach(input => {
            input.addEventListener('change', () => {
                alertContainer.innerHTML = '';
            });
        });
    });

    // sweetalerts for resolve steps
    function showResolveSteps(resolveSteps) {
        const resolveStepsDetails = `${resolveSteps}`;

        Swal.fire({
            title: 'Steps taken to resolve discrepancies',
            html: resolveStepsDetails,
            icon: 'info',
            confirmButtonText: 'Close'
        });
    }
</script>
