@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
@if(Auth::user()->role == 'Administrator' || Auth::user()->role == 'Inventory Manager') 
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <!-- Alert Messages -->
            @include('common.alert')
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-3">
                <h1 class="h2">SALES</h1>
                <a class="btn btn-primary" href="{{ route('sales.create') }}">+ Sale Product</a>
                <a class="btn btn-primary" href="{{ route('return_product.index') }}">Returned Products View</a>
            </div>

            <!-- Search Bar -->
            <form class="d-flex" role="search" id="searchForm">
                <input class="form-control me-2" type="search" placeholder="Search by Sales ID" aria-label="Search" id="searchInput">
                <button class="btn btn-outline-success" type="submit">Search</button>
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
                        <th>Total Amount</th>
                        <th>Sales Timestamp</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesJoined as $data)
                        <tr>
                            <td>{{ $data->sales_id }}</td>
                            <td>{{ $data->first_name }} {{ $data->last_name }}</td>
                            <td>{{ $data->product_name }}</td>
                            <td>{{ $data->category_name }}</td>
                            <td>{{ $data->quantity }}</td>
                            <td>{{ $data->total_amount }}</td>
                            <td>{{ $data->sales_date }}</td>
                            <td>
                                <button type="button" class="btn" onclick="showDescriptionDetail({!! json_encode($data->descriptionArray) !!})">
                                    <u><strong>more info.</strong></u>
                                </button>
                            </td>
                            <td>
                                @if ($data->sales_date > $deadline)
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#returnModal{{ $data->sales_id }}">
                                        Return
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary" disabled>
                                        Return
                                    </button>
                                @endif
                            </td>
                        </tr>
                        <!-- Return Modal for Each Product -->
                        <div class="modal fade" id="returnModal{{ $data->sales_id }}" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="returnModalLabel">Return Product</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form id="returnForm{{ $data->sales_id }}" action="{{ route('return_product.process', $data->sales_id) }}" method="POST">

                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $data->product_id }}">
                                        <input type="hidden" name="sales_id" value="{{ $data->sales_id }}">

                                        <div id="product-info" class="mb-3">
                                            <p><strong>Product Details:</strong></p>
                                            <p><strong>Product Name:</strong> <span id="product_name">{{ $data->product_name }}</span></p>
                                            <p><strong>Purchase Quantity:</strong> <span id="quantity">{{ $data->quantity }}</span></p>
                                            @php
                                                // Find the relevant inventory record for this product
                                                $currentInventory = $inventory->where('product_id', $data->product_id)->first();
                                            @endphp

                                            <p id="price-info">
                                                <strong>Sale Price per Unit: ₱</strong>
                                                <span id="sales_price">
                                                    {{ $currentInventory->sale_price_per_unit ?? 'N/A' }}  <!-- Default to 'N/A' if not set -->
                                                </span>
                                            </p>
                                            <p id="total-info"><strong>Total Amount: </strong><span id="total_amount">{{ $data->total_amount }}</span></p>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="return_quantity">Quantity to be Returned</label>
                                                <input type="number" class="form-control" name="return_quantity" value="{{ old('return_quantity') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="return_reason">Reason</label>
                                                <input type="text" class="form-control" name="return_reason" value="{{ old('return_reason') }}" required>
                                            </div>
                                        </div>

                                        <!-- Modal Validation Error Alert Message-->
                                        @if ($errors->any() && old('sales_id') == $data->sales_id)
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <script>
                                            $(document).ready(function() {
                                                $('#returnModal{{ $data->sales_id }}').modal('show');
                                            });
                                        </script>
                                        @endif

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Confirm Return Product</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No sales found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </main>
    </div>
@endif
@endsection

<!-- JavaScript for Supplier Details and Live Search -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    function showDescriptionDetail(descriptionArray) {
        const { color = 'N/A', size = 'N/A', description = 'N/A' } = descriptionArray;
        const descriptionDetails = `
            <strong>Color:</strong> ${color}<br>
            <strong>Size:</strong> ${size}<br>
            <strong>Description:</strong> ${description}<br>
        `;

        Swal.fire({
            title: 'Highlights',
            html: descriptionDetails,
            icon: 'info',
            confirmButtonText: 'Close'
        });
    }

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
                                        <button type="button" class="btn" onclick="showDescriptionDetail(${JSON.stringify(sale.descriptionArray)})">
                                            <u><strong>more info.</strong></u>
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#returnModal${sale.sales_id}">
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
