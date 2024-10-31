
@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
@if(Auth::user()->role == 'Administrator' || Auth::user()->role == 'Auditor') 
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
                        <th>Supplier ID</th>
                        <th>Action</th>
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
                                    <u><strong>{{ $data->supplier_id }}</strong></u>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateModal{{ $data->inventory_id }}">
                                    Audit
                                </button>
                            </td>
                        </tr>
                        <!-- Restock Modal for Each Product -->
                        <div class="modal fade" id="updateModal{{ $data->inventory_id }}" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Audit Inventory</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('inventory.audit.update', $data->inventory_id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="inventory_id" value="{{ $data->inventory_id }}">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="previous_quantity">In Stock</label>
                                                <input name="previous_quantity" value="{{ $data->in_stock }}" pattern="^\d{1,6}$" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="new_quantity">New Quantity</label>
                                                <input type="number" name="new_quantity" placeholder="New Quantity" pattern="^\d{1,6}$" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="reason">Reason for Audit</label>
                                                <input type="text" name="reason" placeholder="Reason for Audit" pattern="^[a-zA-Z0-9\s\.,\-]{1,30}$" required>
                                            </div>
                                        </div>
                                        <!-- Modal Validation Error Alert Message-->
                                        @if ($errors->any() && old('inventory_id') == $data->inventory_id)
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Audit</button>
                                        </div>
                                    </form>
                                    
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
</script>
