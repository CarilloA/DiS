@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
@if(Auth::user()->credential->role == 'Administrator' || Auth::user()->credential->role == 'Inventory Manager') <!-- Check if user is an administrator or inventory manager-->
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <!-- Alert Messages -->
                @include('common.alert')
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                    <h1 class="h2">INVENTORY</h1>
                </div>

                <!-- Table Section -->
                <table class="table table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>
                                <a class="text-black d-block py-2 px-4" href="{{ route('inventory.create') }}">+ Add Product</a> 
                            </th>
                            <form method="POST" action="{{ url('report') }}" enctype="multipart/form-data">
                                @csrf
                                <th>
                                    <input type="date" /><p>-</p><input type="date" />
                                    <button type="submit" name="create" class="btn">
                                        <span class="input-group-text">
                                            <i class="fa-solid fa-print"></i>
                                        </span>
                                    </button>
                                </th> <!-- date range picker -->
                            </form>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Unit Price</th>
                            <th>UoM</th>
                            <th>Quantity</th>
                            <th>Reorder Level</th>
                            <th>Date & Time</th>
                            <th colspan="2" style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($inventoryJoined) > 0) <!-- Check if inventoryJoined is not null and not empty -->
                            @foreach($inventoryJoined as $data)
                                <tr>
                                    <td>{{ $data->product_name }}</td>
                                    <td>{{ $data->category_name }}</td>
                                    <td>{{ $data->description }}</td>
                                    <td>{{ $data->unit_price }}</td>
                                    <td>{{ $data->UoM }}</td>
                                    <td>{{ $data->quantity_in_stock }}</td>
                                    <td>{{ $data->reorder_level }}</td>
                                    <td>{{ $data->updated_at }}</td>
                                    <td>
                                        <a href="{{ url('edit_product/'. $data->product_id) }}" class="btn">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-pen-to-square" style="color: blue;"></i>
                                            </span>
                                        </a>
                                        <!-- Trigger the modal with a button -->
                                        <button type="button" class="btn" data-toggle="modal" data-target="#deleteModal{{ $data->product_id }}">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-trash" style="color: red;"></i>
                                            </span>
                                        </button>
                                    </td>

                                    <!-- Modal -->
                                    <div id="deleteModal{{ $data->product_id }}" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Confirm Deletion</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ url('delete/'.$data->product_id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')

                                                        <!-- Admin Username Input -->
                                                        <div class="form-group">
                                                            <label for="username">Username</label>
                                                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username_{{ $data->product_id }}" name="username" required>
                                                            @error('username')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>

                                                        <!-- Admin Password Input -->
                                                        <div class="form-group">
                                                            <label for="password">Password</label>
                                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password_{{ $data->product_id }}" name="password" required>
                                                            @error('password')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>

                                                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">No inventory found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </main>
        </div>
    </div>
@endif
@endsection

<!-- JavaScript to clear the input fields when the modal is closed -->
<script>
    $(document).ready(function(){
        // Loop through each delete modal
        @foreach($userJoined as $data)
        $('#deleteModal{{ $data->user_id }}').on('hidden.bs.modal', function () {
            // Clear input fields
            $('#admin_username_{{ $data->user_id }}').val('');
            $('#admin_password_{{ $data->user_id }}').val('');
        });
        @endforeach
    });
</script>