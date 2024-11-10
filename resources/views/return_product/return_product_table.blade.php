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
@if(Auth::user()->role === 'Administrator' || Auth::user()->role === 'Inventory Manager')
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="main-content">
                <!-- Alert Messages -->
            @include('common.alert')
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1 class="h2 mb-4">Returned Product</h1>
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
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-success ms-2" onclick="">
                    <i class="fa-solid fa-print"></i> Scrap Product
                </button>
            </div>

            <!-- Table Section -->
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Ref. No.</th>
                        <th>Seller</th>
                        <th>Product Name</th>
                        <th>Returned Quantity</th>
                        <th>Total Returned Amount</th>
                        <th>Returned Reason</th>
                        <th>Returned Timestamp</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnProductJoined as $data)
                        <tr>
                            <td>{{ $data->sales_id }}</td>
                            <td>{{ $data->first_name }} {{ $data->last_name }}</td>
                            <td>{{ $data->product_name }}</td>
                            <td>{{ $data->return_quantity }}</td>
                            <td>{{ $data->total_return_amount }}</td>
                            <td>{{ $data->return_reason }}</td>
                            <td>{{ $data->return_date }}</td>
                                <td><button type="button" class="btn btn-warning" data-toggle="modal" data-target="#disposalProductModal{{ $data->return_product_id }}">
                                    Dispose
                                </button></td>
                        </tr>
                        <!-- Dispose Product Modal -->
                        <div class="modal fade" style="color: black" id="disposalProductModal{{ $data->return_product_id }}" tabindex="-1" role="dialog" aria-labelledby="storeRestockModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Confirm Product Disposal</h4>
                                        <button type="button" class="close custom-close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('dispose_product', $data->return_product_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <input type="hidden" name="product_name" value="{{ $data->product_name }}">
                                            <input type="hidden" name="return_quantity" value="{{ $data->return_quantity }}">
                                            {{-- to identofy which modal to open to display error alert --}}
                                            <input type="hidden" name="user_id" value="{{ $data->user_id }}">

                                            <!-- Conpfirm Username Input -->
                                            <div class="form-group">
                                                <label for="confirm_username">Confirm Username</label>
                                                <input type="text" class="form-control" id="confirm_username_{{ $data->user_id }}" name="confirm_username" required>
                                            </div>

                                            <!-- Confirm Password Input -->
                                            <div class="form-group">
                                                <label for="confirm_password">Confirm Password</label>
                                                <input type="password" class="form-control" id="confirm_password_{{ $data->user_id }}" name="confirm_password" required>
                                            </div>

                                            <!-- Modal Validation Error Alert Message-->
                                            @if ($errors->any() && old('user_id') == $data->user_id)
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <script>
                                                    $(document).ready(function() {
                                                        $('#disposalProductModal{{ $data->user_id }}').modal('show');
                                                    });
                                                </script>
                                            @endif

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No returned products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </main>
    </div>
@endif
@endsection
