@extends('layouts.app')
@include('common.navbar')

@section('content')
<style>
    .card {
        background-color: #34495e; /* Darker card background */
        border: none; /* Remove border */
        border-radius: 8px; /* Rounded corners */
    }
    .input-group-text {
        background-color: #74e39a; /* Input group background */
        border: none; /* Remove borders */
        color: #0f5132; /* Dark text */
    }
    .btn-primary {
        background-color: #74e39a; /* Green button */
        color: black;
        border: none; /* Remove button borders */
    }
    .btn-primary:hover {
        background-color: #0f5132; /* Darker green on hover */
    }
    .form-control {
        background-color: #fff; /* White input background */
        color: black; /* Black text */
        border: 1px solid #444; /* Subtle border */
    }
    .form-control:focus {
        border-color: #28a745; /* Green border on focus */
        box-shadow: none; /* Remove default shadow */
    }
    .progress {
        height: 20px; /* Height of the progress bar */
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center text-white">{{ __('Create Product') }}</div>
                <div class="card-body">
                    <!-- Alert Messages -->
                    @include('common.alert')
                    <form method="POST" action="{{ url('purchase') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Product Details -->
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="input-group-text" for="product_name">
                                    <i class="fa-solid fa-box-open"></i> Product Name
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
                                    <i class="fa fa-table-list"></i> Category
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
                                    <i class="fa-solid fa-paintbrush"></i> Color
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
                                    <i class="fa-solid fa-ruler"></i> Size
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
                                    <i class="fa-solid fa-pen-to-square"></i> Description
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
                                    <i class="fa-solid fa-pen-to-square"></i> Purchase Price Per Unit
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
                                    <i class="fa-solid fa-pen-to-square"></i> Sale Price Per Unit
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
                                    <i class="fa-solid fa-scale-balanced"></i> Unit of Measure
                                </label>
                                <input id="unit_of_measure" type="text" class="form-control @error('unit_of_measure') is-invalid @enderror" name="unit_of_measure" value="{{ old('unit_of_measure') }}" pattern="^[a-zA-Z\s]{1,15}$" required>
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
                                    <i class="fa-solid fa-warehouse"></i> Purchased Quantity
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
                                    <i class="fa-solid fa-warehouse"></i> Reorder Level
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
                                    <i class="fa-solid fa-warehouse"></i> Aisle Number
                                </label>
                                <select name="aisle_number" id="aisle_number" class="form-control @error('aisle_number') is-invalid @enderror" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                                @error('aisle_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="input-group-text" for="cabinet_level">
                                    <i class="fa-solid fa-warehouse"></i> Cabinet Level
                                </label>
                                <select name="cabinet_level" id="cabinet_level" class="form-control @error('cabinet_level') is-invalid @enderror" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                @error('cabinet_level')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                                <div class="col-md-4">
                                    <label class="input-group-text" for="product_quantity">
                                        <i class="fa-solid fa-boxes"></i> Product Quantity Stored
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
                                    <i class="fa-solid fa-industry"></i> Company Name
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
                                    <i class="fa-solid fa-industry"></i> Contact Person
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
                                    <i class="fa-solid fa-industry"></i> Email
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
                                    <i class="fa-solid fa-industry"></i> Mobile Number
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
                                    <i class="fa-solid fa-industry"></i> Address
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
                                    {{ __('ADD') }}
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
