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
                <h1 class="h2">INVENTORY</h1>
                <a class="btn btn-primary" href="{{ route('inventory.create') }}">+ Add Product</a>
            </div>

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
                        <th>Name</th>
                        <th>Category</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Unit of Measure</th>
                        <th>In Stock</th>
                        <th>Reorder Level</th>
                        <th>Date & Time</th>
                        <th>Description</th>
                        <th>Supplier</th>
                        <th colspan="2" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventoryJoined as $data)
                        <tr>
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
                                    <u><strong>more info.</strong></u>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="btn" onclick="showSupplierDetail('{{ $data->company_name }}', '{{ $data->contact_person }}', '{{ $data->mobile_number }}', '{{ $data->email }}', '{{ $data->address }}')">
                                    <u><strong>{{ $data->contact_person }}</strong></u>
                                </button>
                            </td>
                            <td>
                                <a href="{{ url('edit_product/'.$data->product_id) }}" class="btn">
                                    <i class="fa-solid fa-pen-to-square" style="color: blue;"></i>
                                </a>
                                <button type="button" class="btn" data-toggle="modal" data-target="#deleteModal{{ $data->product_id }}">
                                    <i class="fa-solid fa-trash" style="color: red;"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Delete Modal -->
                        <div id="deleteModal{{ $data->product_id }}" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Confirm Deletion</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ url('delete/'.$data->product_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <input type="text" class="form-control" name="username" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" class="form-control" name="password" required>
                                            </div>
                                            <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No inventory found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
</script>
