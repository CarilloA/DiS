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
                        <th>Name</th>
                        <th>Category</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Unit of Measure</th>
                        <th>In Stock</th>
                        <th>Reorder Level</th>
                        <th>Date & Time</th>
                        <th>Description</th>
                        <th>Supplier Details</th>
                        <th>Location</th>
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
                                    <u><strong>more info.</strong></u>
                                </button>
                            </td>
                            <?php $storeStock = $data->in_stock - $data->product_quantity; ?>
                            <td>
                                <button type="button" class="btn" onclick="showStockroomDetail('{{ $storeStock }}', '{{ $data->aisle_number }}', '{{ $data->cabinet_level }}', '{{ $data->product_quantity }}', '{{ $data->category_name }}')">
                                    <u><strong>more info.</strong></u>
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
</script>
