@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
@if(Auth::user()->role == 'Administrator' || Auth::user()->role == 'Inventory Manager') 
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <!-- Alert Messages -->
            @include('common.alert')

            <!-- Date Range Picker -->
            <form method="POST" action="{{ url('report') }}" enctype="multipart/form-data" class="mb-3">
                @csrf
                <div class="input-group">
                    <input type="date" name="start_date" class="form-control" required>
                    <span class="input-group-text">-</span>
                    <input type="date" name="end_date" class="form-control" required>
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </div>
            </form>

            <!-- Table Section -->
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Ref. No.</th>
                        <th>Seller</th>
                        <th>Product Name</th>
                        <th>Return Quantity</th>
                        <th>Return Reason</th>
                        <th>Return Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnProductJoined as $data)
                        <tr>
                            <td>{{ $data->product_id }}</td>
                            <td>{{ $data->first_name }} {{ $data->last_name }}</td>
                            <td>{{ $data->product_name }}</td>
                            <td>{{ $data->return_quantity }}</td>
                            <td>{{ $data->return_reason }}</td>
                            <td>{{ $data->return_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No returned product found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </main>
    </div>
@endif
@endsection