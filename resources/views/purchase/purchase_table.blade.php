@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
@if(Auth::user()->role == 'Administrator' || Auth::user()->role == 'Inventory Manager') 
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            @include('common.alert')
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-3">
                <h1 class="h2">PURCHASE PRODUCTS</h1>
                <a class="btn btn-primary" href="{{ route('purchase.create') }}">+ Add Product</a>
            </div>

            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Purchase Price</th>
                        <th>Unit of Measure</th>
                        <th>Purchase Quantity</th>
                        <th>Reorder Level</th>
                        <th>Date & Time</th>
                        <th>Description</th>
                        <th>Supplier ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productJoined as $data)
                    <tr>
                        <td>{{ $data->product_id }}</td>
                        <td>{{ $data->product_name }}</td>
                        <td>{{ $data->category_name }}</td>
                        <td>{{ $data->purchase_price_per_unit }}</td>
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
                            <button type="button" class="btn" onclick="showSupplierDetail('{{ $data->supplier_id }}', '{{ $data->company_name }}', '{{ $data->contact_person }}', '{{ $data->mobile_number }}', '{{ $data->email }}', '{{ $data->address }}')">
                                <u><strong>{{ $data->supplier_id }}</strong></u>
                            </button>
                        </td>
                        <td>
                            @if ($data->in_stock <= $data->reorder_level)
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#restockModal{{ $data->product_id }}">
                                    Restock
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    Restock
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Restock Modal for Each Product -->
                    <div class="modal fade" id="restockModal{{ $data->product_id }}" tabindex="-1" role="dialog" aria-labelledby="restockModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="restockModalLabel">Restock Product</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="restockForm{{ $data->product_id }}" action="{{ route('restock_product') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $data->product_id }}">
                                    <input type="hidden" name="supplier_id" value="{{ $data->supplier_id }}">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="purchase_price_per_unit">Purchase Price Per Unit</label>
                                            <input type="text" class="form-control" name="purchase_price_per_unit" value="{{ old('purchase_price_per_unit') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="unit_of_measure">Unit of Measure</label>
                                            <input type="text" class="form-control" name="unit_of_measure" value="{{ old('unit_of_measure') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="quantity">Quantity</label>
                                            <input type="text" class="form-control" name="quantity" value="{{ old('quantity') }}" required>
                                        </div>
                                        <div class="form-group">
                                           <input type="hidden" name="update_supplier" value="0">  <!-- Hidden input to default to false -->
                                            <input type="checkbox" id="update_supplier_checkbox{{ $data->supplier_id }}" name="update_supplier" value="1" {{ old('update_supplier') ? 'checked' : '' }}> <!-- Checkbox -->
                                            <label for="update_supplier_checkbox{{ $data->supplier_id }}">Update supplier</label>
                                        </div>
                                        
                                        <div id="supplier_details_section{{ $data->supplier_id }}" style="display: none;">
                                            <div class="form-group">
                                                <label for="company_name">Company Name</label>
                                                <input type="text" class="form-control" name="company_name" value="{{ old('company_name') }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="contact_person">Contact Person</label>
                                                <input type="text" class="form-control" name="contact_person" value="{{ old('contact_person') }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="mobile_number">Mobile Number</label>
                                                <input type="number" class="form-control" name="mobile_number" value="{{ old('mobile_number') }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal Validation Error Alert Message-->
                                    @if ($errors->any() && old('product_id') == $data->product_id)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                
                                    <script>
                                        $(document).ready(function() {
                                            $('#restockModal{{ $data->product_id }}').modal('show');
                                            
                                            // Check if the checkbox is checked and show/hide the supplier details section accordingly
                                            if ($('#update_supplier_checkbox{{ $data->supplier_id }}').is(':checked')) {
                                                $('#supplier_details_section{{ $data->supplier_id }}').show();
                                            }
                                            
                                            $('[id^="update_supplier_checkbox"]').change(function() {
                                                const supplierId = $(this).attr('id').match(/\d+/)[0]; // Get supplier ID from checkbox ID
                                                const supplierDetailsSection = `#supplier_details_section${supplierId}`;

                                                // Toggle supplier details section visibility based on checkbox state
                                                $(supplierDetailsSection).toggle(this.checked);
                                                $(supplierDetailsSection + ' input').prop('required', this.checked);
                                            });
                                        });
                                    </script>
                                @endif

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Restock</button>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </main>
    </div>
@endif


@endsection

<!-- JavaScript for Supplier Details and Restock Modal -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function showDescriptionDetail(color, size, description) {
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

    function showSupplierDetail(supplierID, companyName, contactPerson, mobileNumber, email, address) {
        const supplierDetails = `
            <strong>Supplier ID:</strong> ${supplierID}<br>
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

    $(document).ready(function() {
        $('[id^="update_supplier_checkbox"]').change(function() {
            const supplierId = $(this).attr('id').match(/\d+/)[0]; // Get supplier ID from checkbox ID
            const supplierDetailsSection = `#supplier_details_section${supplierId}`;

            // Toggle supplier details section visibility based on checkbox state
            $(supplierDetailsSection).toggle(this.checked);
            $(supplierDetailsSection + ' input').prop('required', this.checked);
        });
    });
</script>
