@extends('layouts.app')
@include('common.navbar')

@section('content')
<style>
    body {
        background-image: url('/storage/images/bg-photo.jpeg');
        background-size: cover; /* Cover the entire viewport */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat; /* Prevent the image from repeating */
        background-color: #1a1a1a; /* Dark background */
        color: #fff; /* Light text color */
    }
    .card {
        
        background-color: #565656; /* Card background */
        border: none; /* Remove border */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }
    .input-group-text {
        background-color: #3a8f66; /* input group background */
        border: none; /* Remove borders */
        color: #fff; /* White text */
    }
    .btn-primary {
        background-color: #3a8f66; /* Green button */
        color: #fff; /* White text */
        border: none; /* Remove button borders */
    }
    .btn-primary:hover {
        background-color: #2f6b5a; /* Darker green on hover */
    }
    .btn-secondary {
        background-color: #3a8f66; /* Dark background for role selection */
        color: #fff; /* White text */
        border: none;
    }
    .btn-secondary:hover {
        background-color: #2f6b5a; /* Darker green on hover */
    }
    .form-control {
        background-color: #fff; /* White input background */
        color: #000; /* Black text */
        border: 1px solid #444; /* Subtle border */
    }
    .form-control:focus {
        background-color: #fff; /* Focus background */
        color: #000; /* Black text */
        border-color: #3a8f66; /* Green border on focus */
        box-shadow: none; /* Remove default shadow */
    }

    .form-control {
        background-color: #212529; /* Change input background */
        color: #fff; /* White text */
        border: 1px solid #444; 
        border-radius: 4px; /* Optional: Rounded corners */
    }
    .form-control:focus {
        background-color: #212529; 
        color: #fff; 
        border-color: #3a8f66; 
        box-shadow: none; 
    }

    /* Placeholder styling */
    .form-control::placeholder {
        color: #bbb; /* Light grey for placeholder text */
        opacity: 1; /* Ensures the opacity is fully opaque */
    }
    .text {
        color: #fff;
    }

    /* Custom styling for the select dropdown */
    .custom-select select {
        background-color: #212529; /* Black background for select */
        color: white; /* White text */
        border: 1px solid #444; /* Subtle border */
        appearance: none; /* Remove default arrow */
        border-radius: 4px;
        position: relative;
    }

    /* Add a custom arrow using a background image or pseudo-element */
    .custom-select {
        position: relative;
    }

    .custom-select::after {
        content: '▼'; /* Custom arrow */
        color: white; /* White arrow */
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    /* Style dropdown options */
    .custom-select select option {
        background-color: #333; /* Dark background for options */
        color: white; /* White text */
        padding: 8px;
    }

    /* On hover, options can change color */
    .custom-select select option:hover {
        background-color: #3a8f66; /* Slightly greenish background on hover */
        color: white;
    }

</style>

<div class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center text-white" style="background-color: #3a8f66; color:#fff; font-weight: bold; ">{{ __('Create Product') }}</div>
                <div class="card-body">
                    <!-- Alert Messages -->
                    @include('common.alert')
                    <form method="POST" action="{{ url('purchase') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Product Details -->
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="input-group-text" for="product_name">
                                    <i class="fa-solid fa-box-open" style="margin-right: 5px;"></i>Product Name
                                </label>
                                <input id="product_name" type="text" class="form-control @error('product_name') is-invalid @enderror" name="product_name" value="{{ old('product_name') }}" pattern="^[a-zA-Z0-9\s\-]{1,30}$" required>
                                @error('product_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="input-group-text" for="category_name">
                                    <i class="fa fa-table-list" style="margin-right: 5px;"></i> Category
                                </label>
                                <input id="category_name" type="text" class="form-control @error('category_name') is-invalid @enderror" name="category_name" value="{{ old('category_name') }}" pattern="^[a-zA-Z\s]{1,30}$" required>
                                @error('category_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="input-group-text" for="color">
                                    <i class="fa-solid fa-paintbrush" style="margin-right: 5px;"></i> Color
                                </label>
                                <input id="color" type="text" class="form-control @error('color') is-invalid @enderror" name="color" value="{{ old('color', isset($descriptionArray['color']) ? $descriptionArray['color'] : '') }}" pattern="^[a-zA-Z\s]{1,20}$">
                                @error('color')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        
                            <div class="col-md-4">
                                <label class="input-group-text" for="size">
                                    <i class="fa-solid fa-ruler" style="margin-right: 5px;"></i> Size
                                </label>
                                <input id="size" type="text" class="form-control @error('size') is-invalid @enderror" name="size" value="{{ old('size', isset($descriptionArray['size']) ? $descriptionArray['size'] : '') }}" pattern="^[a-zA-Z0-9\s\-]{1,15}$">
                                @error('size')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        
                            <div class="col-md-4">
                                <label class="input-group-text" for="description">
                                    <i class="fa-solid fa-pen-to-square" style="margin-right: 5px;"></i> Description
                                </label>
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ old('description', isset($descriptionArray['description']) ? $descriptionArray['description'] : '') }}" pattern="^[a-zA-Z0-9\s\-\.,]{1,255}$">
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="input-group-text" for="purchase_price_per_unit">
                                    <i class="fa-solid fa-pen-to-square" style="margin-right: 5px;"></i> Purchase Price Per Unit
                                </label>
                                <input id="purchase_price_per_unit" type="text" class="form-control @error('purchase_price_per_unit') is-invalid @enderror" name="purchase_price_per_unit" value="{{ old('purchase_price_per_unit') }}" pattern="^\d{1,6}(\.\d{1,2})?$" required>
                                @error('purchase_price_per_unit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="input-group-text" for="sale_price_per_unit">
                                    <i class="fa-solid fa-pen-to-square" style="margin-right: 5px;"></i> Sale Price Per Unit
                                </label>
                                <input id="sale_price_per_unit" type="text" class="form-control @error('sale_price_per_unit') is-invalid @enderror" name="sale_price_per_unit" value="{{ old('sale_price_per_unit') }}" pattern="^\d{1,6}(\.\d{1,2})?$" required>
                                @error('sale_price_per_unit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="input-group-text" for="unit_of_measure">
                                    <i class="fa-solid fa-scale-balanced" style="margin-right: 5px;"></i> Unit of Measure
                                </label>
                                <div class="custom-select">
                                    <select name="unit_of_measure" id="unit_of_measure" class="form-control  @error('unit_of_measure') is-invalid @enderror" required>
                                        <option value="pcs">piece</option> 
                                        <option value="pair">pair</option>
                                        <option value="set">set</option>
                                        <option value="box">box</option> 
                                        <option value="pack">pack</option>
                                        <option value="kit">kit</option>
                                        <option value="liter">liter</option>
                                        <option value="gallon">gallon</option>
                                        <option value="roll">roll</option>
                                        <option value="meter">meter</option>
                                    </select>
                                </div>
                                @error('unit_of_measure')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="input-group-text" for="in_stock">
                                    <i class="fa-solid fa-warehouse" style="margin-right: 5px;"></i> Purchased Quantity
                                </label>
                                <input id="in_stock" type="text" class="form-control @error('in_stock') is-invalid @enderror" name="in_stock" value="{{ old('in_stock') }}" pattern="^\d{1,6}$" required>
                                @error('in_stock')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="input-group-text" for="reorder_level">
                                    <i class="fa-solid fa-warehouse" style="margin-right: 5px;"></i> Reorder Level
                                </label>
                                <input id="reorder_level" type="text" class="form-control @error('reorder_level') is-invalid @enderror" name="reorder_level" value="{{ old('reorder_level') }}" pattern="^\d{1,6}$" required>
                                @error('reorder_level')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                         <!-- Stockroom Details -->
                         <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="input-group-text" for="aisle_number">
                                    <i class="fa-solid fa-warehouse" style="margin-right: 5px;"></i> Aisle Number
                                </label>
                                <div class="custom-select">
                                    <select name="aisle_number" id="aisle_number" class="form-control @error('aisle_number') is-invalid @enderror" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                                @error('aisle_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="input-group-text" for="cabinet_level">
                                    <i class="fa-solid fa-warehouse" style="margin-right: 5px;"></i> Cabinet Level
                                </label>
                                <div class="custom-select">
                                    <select name="cabinet_level" id="cabinet_level" class="form-control @error('cabinet_level') is-invalid @enderror" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                                @error('cabinet_level')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                                <div class="col-md-4">
                                    <label class="input-group-text" for="product_quantity">
                                        <i class="fa-solid fa-boxes" style="margin-right: 5px;"></i> Product Quantity Stored
                                    </label>
                                    <input id="product_quantity" type="number" class="form-control @error('product_quantity') is-invalid @enderror" name="product_quantity" value="{{ old('product_quantity') }}" min="1" required>
                                    @error('product_quantity')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                        </div>

                        <!-- Supplier Details -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="input-group-text" for="company_name">
                                    <i class="fa-solid fa-industry" style="margin-right: 5px;"></i> Supplier
                                </label>
                                <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" pattern="^[a-zA-Z0-9\s\-]{1,30}$" required>
                                @error('company_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="input-group-text" for="contact_person">
                                    <i class="fa-solid fa-industry" style="margin-right: 5px;"></i> Contact Person
                                </label>
                                <input id="contact_person" type="text" class="form-control @error('ccontact_person') is-invalid @enderror" name="contact_person" value="{{ old('contact_person') }}" pattern="^[a-zA-Z\s]{1,30}$" required>
                                @error('contact_person')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="input-group-text" for="email">
                                    <i class="fa-solid fa-industry" style="margin-right: 5px;"></i> Email
                                </label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="input-group-text" for="mobile_number">
                                    <i class="fa-solid fa-industry" style="margin-right: 5px;"></i> Mobile Number
                                </label>
                                <input id="mobile_number" type="text" class="form-control @error('mobile_number') is-invalid @enderror" name="mobile_number" pattern="^09\d{9}$" value="{{ old('mobile_number') }}" required>
                                @error('mobile_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="input-group-text" for="address">
                                    <i class="fa-solid fa-industry" style="margin-right: 5px;"></i> Address
                                </label>
                                <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" pattern="^[a-zA-Z0-9\s.,\-]{1,100}$" required>
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" name="create" class="btn btn-primary">
                                    {{ __('Add Product') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
