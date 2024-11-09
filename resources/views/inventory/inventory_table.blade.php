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
@if(Auth::user()->role == 'Administrator' || Auth::user()->role == 'Inventory Manager') 
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="main-content">
                <!-- Alert Messages -->
            @include('common.alert')
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1 class="h2">Inventory Management</h1>
            </div>

            {{-- Generate Report --}}
            <form method="POST" action="{{ url('inventory_report') }}" enctype="multipart/form-data" class="mb-4 report-form">
                @csrf
                <div class="input-group mb-3">
                    <input type="date" class="custom-date-picker" name="start_date" class="form-control" placeholder="Start Date" max="{{ date('Y-m-d') }}"  required>
                    <span class="input-group-text">TO</span>
                    <input type="date" class="custom-date-picker" name="end_date" class="form-control" placeholder="End Date" max="{{ date('Y-m-d') }}"  required>
                    <button type="submit" class="btn btn-success ms-2">
                        <i class="fa-solid fa-print"></i> Generate Report
                    </button>
                </div>
            </form>

            <!-- Table Section -->
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Product No.</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Unit of Measure</th>
                        <th>In Stock</th>
                        <th>Reorder Level</th>
                        <th>Timestamp</th>
                        <th>Description</th>
                        <th>Supplier Details</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventoryJoined as $data)
                        <tr>
                            <td>{{ $data->product_id }}</td>
                            <td>{{ $data->product_name }}</td>
                            <td>{{ $data->category_name }}</td>
                            <td>{{ $data->purchase_price_per_unit }}</td>
                            <td>{{ $data->sale_price_per_unit }}</td>
                            <td>{{ $data->unit_of_measure }}</td>
                            <td>{{ $data->in_stock }}</td>
                            <td>{{ $data->reorder_level }}</td>
                            <td>{{ $data->updated_at }}</td>
                            <td>
                                <button type="button" class="btn" onclick="showDescriptionDetail('{{ $data->descriptionArray['color'] ?? 'N/A' }}', '{{ $data->descriptionArray['size'] ?? 'N/A' }}', '{{ $data->descriptionArray['description'] ?? 'N/A' }}')">
                                    <strong style="color: white; text-decoration: none; font-weight: normal;" >more info.</strong>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="btn" onclick="showSupplierDetail('{{ $data->company_name }}', '{{ $data->contact_person }}', '{{ $data->mobile_number }}', '{{ $data->email }}', '{{ $data->address }}')">
                                    <strong style="color: white; text-decoration: none; font-weight: normal;">more info.</strong>
                                </button>
                            </td>
                            <?php $storeStock = $data->in_stock - $data->product_quantity; ?>
                            <td>
                                <button type="button" class="btn" onclick="showStockroomDetail('{{ $storeStock }}', '{{ $data->aisle_number }}', '{{ $data->cabinet_level }}', '{{ $data->product_quantity }}', '{{ $data->category_name }}')">
                                    <strong style="color: white; text-decoration: none; font-weight: normal;">more info.</strong>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No inventory found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            
        </main>
    </div>
@endif
@endsection

<!-- JavaScript for Supplier Details -->
<script>
    function showDescriptionDetail(color, size, description) {
        const descriptionDetails = `
            <strong>Company Name:</strong> ${color}<br>
            <strong>Contact Person:</strong> ${size}<br>
            <strong>Mobile Number:</strong> ${description}<br>
        `;

        Swal.fire({
            title: 'Highlights',
            html: descriptionDetails,
            icon: 'info',
            confirmButtonText: 'Close'
        });
    }

    function showSupplierDetail(companyName, contactPerson, mobileNumber, email, address) {
        const supplierDetails = `
            <strong>Company Name:</strong> ${companyName}<br>
            <strong>Contact Person:</strong> ${contactPerson}<br>
            <strong>Mobile Number:</strong> ${mobileNumber}<br>
            <strong>Email:</strong> ${email}<br>
            <strong>Address:</strong> ${address}
        `;

        Swal.fire({
            title: 'Supplier Details',
            html: supplierDetails,
            icon: 'info',
            confirmButtonText: 'Close'
        });
    }

    function showStockroomDetail(storeStock, aisleNumber, cabinetLevel, productQuantity, categoryName) {
        const stockroomDetails = `
            <strong>Store Stock:</strong> ${storeStock}<br><br>
            <strong>Stockroom Details</strong><br>
            <strong>Aisle Number:</strong> ${aisleNumber}<br>
            <strong>Cabinet Level:</strong> ${cabinetLevel}<br>
            <strong>Stored Product Quantity:</strong> ${productQuantity}<br>
            <strong>Category Name:</strong> ${categoryName}<br>
        `;

        Swal.fire({
            title: 'Location Details',
            html: stockroomDetails,
            icon: 'info',
            confirmButtonText: 'Close'
        });
    }

    $(document).ready(function(){
        // Loop through each delete modal
        @foreach($userSQL as $data)
        $('#deleteModal{{ $data->user_id }}').on('hidden.bs.modal', function () {
            // Clear input fields
            $('#admin_username_{{ $data->user_id }}').val('');
            $('#admin_password_{{ $data->user_id }}').val('');
        });
        @endforeach
    });

    // Transfer dates to hidden PDF form fields when generating a PDF report
    // document.querySelector('.report-form').addEventListener('submit', function(e) {
    //     document.getElementById('pdf_start_date').value = document.getElementById('start_date').value;
    //     document.getElementById('pdf_end_date').value = document.getElementById('end_date').value;
    // });
</script>
