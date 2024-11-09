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
    .form-control {
        background-color: #212529 !important; /* Grey background for input */
        color: white !important; /* White text */
        border-radius: 5px; /* Rounded corners */
        border: none !important;
    }

    .form-control:focus {
        background-color: #212529; /* Maintain grey background on focus */
        color: white!important; /* White text */
        outline: none; /* Remove default outline */
    }

    .input-group .input-group-text {
        background-color: #198754; /* Background for 'to' text */
        color: white!important; /* Text color */
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
        color: #fff !important; /* This will apply to all text in the modal */
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
@if(Auth::user()->role == 'Administrator' || Auth::user()->role == 'Inventory Manager') 
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="main-content">
                    <!-- Alert Messages -->
                @include('common.alert')
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-3">
                    <h1 class="h2">Sales Management</h1>
                    <div class="d-flex">
                        <a class="btn btn-success" href="{{ route('sales.create') }}">+ Sale Product</a>
                        <a class="btn btn-warning ms-2" href="{{ route('return_product_table') }}" style="margin-left: -5px;">Returned Products View</a>
                    </div>
                </div>

                <!-- Search Bar -->
                <form class="d-flex" role="search" id="searchForm">
                    <input class="form-control me-2" type="search" placeholder="Search by Sales ID" aria-label="Search" id="searchInput">
                    <button class="btn btn-success" type="submit">Search</button>
                </form>
                <div id="searchResults" class="dropdown mt-2" style="display: none;">
                    <ul class="dropdown-menu" id="resultsList"></ul>
                </div>

                <!-- Table to Display Search Results -->
                <table class="table table-responsive mt-4" id="searchResultsTable" style="display: none;">
                    <thead>
                        <tr>
                            <th>Ref. No.</th>
                            <th>Seller</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                            <th>Sales Timestamp</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="selectedSaleDetails">
                        <!-- Selected sales details will appear here -->
                    </tbody>
                </table>

                <!-- Table Section for All Sales -->
                <table class="table table-responsive" id="allSalesTable">
                    <thead>
                        <tr>
                            <th>Ref. No.</th>
                            <th>Seller</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Sale Price</th>
                            <th>Total Amount</th>
                            <th>Sales Timestamp</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentSalesId = null; // Variable to track current sales ID
                        @endphp

                        @forelse($salesGrouped as $sales)
                            @foreach($sales as $index => $data)
                                <tr>
                                    @if($index === 0) <!-- Display this only for the first product -->
                                        <td rowspan="{{ count($sales) }}">{{ $data->sales_id }}</td> <!-- Merge cells for sales_id -->
                                        <td rowspan="{{ count($sales) }}">{{ $data->first_name }} {{ $data->last_name }}</td> <!-- Merge cells for seller -->
                                        <td>{{ $data->product_name }}</td>
                                        <td>{{ $data->category_name }}</td>
                                        <td>{{ $data->sales_quantity }}</td>
                                        <td>{{ $data->sale_price_per_unit }}</td>
                                        <td rowspan="{{ count($sales) }}">{{ $data->total_amount }}</td>
                                        <td>{{ $data->sales_date }}</td>
                                        <td>
                                            <button type="button" class="btn" onclick="showDescriptionDetail('{{ $data->descriptionArray['color'] ?? 'N/A' }}', '{{ $data->descriptionArray['size'] ?? 'N/A' }}', '{{ $data->descriptionArray['description'] ?? 'N/A' }}')">
                                                <strong style="color: white; text-decoration: none; font-weight: normal;" >more info.</strong>
                                            </button>
                                        </td>
                                        <td>
                                            @if ($data->sales_date > $deadline)
                                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#returnModal{{ $data->sales_details_id }}">
                                                    Return
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-warning" disabled>
                                                    Return
                                                </button>
                                            @endif
                                        </td>
                                    @else
                                    <td>{{ $data->product_name }}</td>
                                    <td>{{ $data->category_name }}</td>
                                    <td>{{ $data->sales_quantity }}</td>
                                    <td>{{ $data->sale_price_per_unit }}</td>
                                    <td>{{ $data->sales_date }}</td>
                                    <td>
                                        <button type="button" class="btn" onclick="showDescriptionDetail('{{ $data->descriptionArray['color'] ?? 'N/A' }}', '{{ $data->descriptionArray['size'] ?? 'N/A' }}', '{{ $data->descriptionArray['description'] ?? 'N/A' }}')">
                                            <strong style="color: white; text-decoration: none; font-weight: normal;" >more info.</strong>
                                        </button>
                                    </td>
                                    <td>
                                        @if ($data->sales_date > $deadline)
                                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#returnModal{{ $data->sales_details_id }}">
                                                Return
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-secondary" disabled>
                                                Return
                                            </button>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                            <!-- Return Modal for Each Product -->
                            @foreach ($salesGrouped as $sales)
                                @foreach($sales as $data)
                                <div class="modal fade" id="returnModal{{ $data->sales_details_id }}" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content" style="margin: 20px 15px; background-color:#565656;">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="returnModalLabel">Return Product</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form id="returnForm{{ $data->sales_details_id }}" action="{{ route('return_product.process', $data->sales_details_id) }}" method="POST">

                                                @csrf
                                                <input type="hidden" name="sales_id" value="{{ $data->sales_id }}">
                                                <input type="hidden" name="sales_details_id" value="{{ $data->sales_details_id }}">

                                                <div id="product-info" class="mb-3" style="margin-left: 1em;">
                                                    <p><strong>Sale Details:</strong></p>
                                                    <p><strong>Ref. No.:</strong> <span id="product_name">{{ $data->sales_id }}</span></p>
                                                    <p><strong>Product Name:</strong> <span id="product_name">{{ $data->product_name }}</span></p>
                                                    <p><strong>Purchase Quantity:</strong> <span id="quantity">{{ $data->sales_quantity }}</span></p>

                                                    <p id="price-info">
                                                        <strong>Sale Price per Unit: ₱</strong>
                                                        <span id="sale_price_per_unit" data-price="{{ $data->sale_price_per_unit ?? '0' }}">
                                                            {{ $data->sale_price_per_unit ?? 'N/A' }}
                                                        </span>
                                                    </p>
                                                    <p id="total-info"><strong>Total Purchase Amount: ₱</strong><span id="total_amount">{{ $data->total_amount }}</span></p>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="return_quantity">Quantity to be Returned</label>
                                                        <input type="number" style="color:" class="form-control return-quantity" id="return_quantity_{{ $data->sales_details_id }}" name="return_quantity" pattern="^\d{1,6}$" required>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label for="total_return_amount_{{ $data->sales_details_id }}">Total Amount to be Returned</label>
                                                        <input type="text" class="form-control total-return-amount" id="total_return_amount_{{ $data->sales_details_id }}" name="total_return_amount" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="return_reason">Reason</label>
                                                        <input type="text" class="form-control" name="return_reason" value="{{ old('return_reason') }}" pattern="^[a-zA-Z0-9\s\.,\-]{1,30}$" required>
                                                    </div>
                                                </div>

                                                <!-- Modal Validation Error Alert Message-->
                                                @if ($errors->any() && old('sales_id') == $data->sales_details_id)
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('#returnModal{{ $data->sales_details_id }}').modal('show');
                                                        });
                                                    </script>
                                                @endif

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success" style="color: #fff">Confirm Return Product</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No sales found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </main>
    </div>
@endif
@endsection

<!-- JavaScript for Supplier Details and Live Search -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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

    // Ensure the document is fully loaded before running the script
$(document).ready(function() {
    // Automatically calculate the total return amount when the return quantity is inputted
    $('.return-quantity').each(function() {
        $(this).on('input', function() {
            const saleId = $(this).attr('id').split('_')[2]; // Extract the sales_id from the input's ID
            const returnQuantity = parseFloat($(this).val()) || 0; // Ensure the quantity is a number
            const pricePerUnitElement = $(`#returnModal${saleId} #sale_price_per_unit`);
            const pricePerUnit = parseFloat(pricePerUnitElement.data('price')) || 0; // Ensure price per unit is a number

            // Calculate the total amount to be returned
            const totalReturnAmount = returnQuantity * pricePerUnit;

            // Debugging logs
            console.log("Sale ID:", saleId);
            console.log("Return Quantity:", returnQuantity);
            console.log("Price Per Unit:", pricePerUnit);

            // Update the total return amount field
            $(`#total_return_amount_${saleId}`).val(totalReturnAmount.toFixed(2));
        });
    });
});


    $(document).ready(function() {
        // Handle form submission for search
        $('#searchForm').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission
            let query = $('#searchInput').val(); // Get search input

            $.ajax({
                url: "{{ route('sales.search') }}", // Adjust the route accordingly
                method: "GET",
                data: { query: query },
                success: function(data) {
                    let tableBody = $('#selectedSaleDetails');
                    tableBody.empty(); // Clear the table

                    if (data.length > 0) {
                        // Show search results table and hide all sales table
                        $('#searchResultsTable').show();
                        $('#allSalesTable').hide();

                        // Loop through results and append to the table
                        $.each(data, function(index, sale) {
                            tableBody.append(`
                                <tr>
                                    <td>${sale.sales_id}</td>
                                    <td>${sale.first_name} ${sale.last_name}</td>
                                    <td>${sale.product_name}</td>
                                    <td>${sale.category_name}</td>
                                    <td>${sale.quantity}</td>
                                    <td>${sale.total_amount}</td>
                                    <td>${sale.sales_date}</td>
                                    <td>
                                        <button type="button" class="btn" onclick="showDescriptionDetail('{{ $data->descriptionArray['color'] ?? 'N/A' }}', '{{ $data->descriptionArray['size'] ?? 'N/A' }}', '{{ $data->descriptionArray['description'] ?? 'N/A' }}')">
                                            <strong>more info.</strong>
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#returnModal${ sale.sales_details_id }">
                                            Return
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        // Handle no results case
                        tableBody.append('<tr><td colspan="9" class="text-center">No results found.</td></tr>');
                        $('#searchResultsTable').hide();
                        $('#allSalesTable').show(); // Show all sales table again if no results
                    }
                },
                error: function() {
                    console.log('Error occurred while fetching sales data.');
                }
            });
        });
    });
</script>