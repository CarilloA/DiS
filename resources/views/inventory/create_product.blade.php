@extends('layouts.app')
@include('common.navbar')

@section('content')
<style>
    body {
        background-color: #1a1a1a; /* Dark background */
        color: #f8f9fa; /* Light text color */
    }
    .card {
        background-color: #d3d6d3; /* Card background */
        border: none; /* Remove border */
        border-radius: 8px; /* Rounded corners */
    }
    .input-group-text {
        background-color: #74e39a; /* input group background */
        border: none; /* Remove borders */
        color: #0f5132; /* White text */
    }
    .btn-primary {
        background-color: #74e39a; /* Green button */
        color: black;
        border: none; /* Remove button borders */
    }
    .btn-primary:hover {
        background-color: #0f5132; /* Darker green on hover */
    }
    .btn-secondary {
        background-color: #74e39a; /* Dark background for role selection */
        color: #0f5132;
        border: none;
    }
    .btn-secondary:hover {
        background-color: #0f5132; /* Green on hover */
    }
    .form-control {
        background-color: white; /* Darker input background */
        color: black; /* White text */
        border: 1px solid #444; /* Subtle border */
    }
    .form-control:focus {
        background-color: white; /* Focus background */
        color: black;
        border-color: #28a745; /* Green border on focus */
        box-shadow: none; /* Remove default shadow */
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">{{ __('Create Product') }}</div>
                <div class="card-body">
                    <!-- Alert Messages -->
                    @include('common.alert')
                    <form method="POST" action="{{ url('inventory') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-box-open"></i><label class="ms-2">Product Name</label>
                                </span>
                                <input id="product_name" type="text" class="form-control @error('product_name') is-invalid @enderror" name="product_name" placeholder="Format Sample: name" value="{{ old('product_name') }}" required>

                                @error('product_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa fa-table-list"></i><label class="ms-2">Category</label>
                                </span>
                                <input id="category_name" type="text" class="form-control @error('category_name') is-invalid @enderror" name="category_name" placeholder="Format Sample: category" value="{{ old('category_name') }}" required>

                                @error('category_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-pen-to-square"></i><label class="ms-2">Description</label>
                                </span>
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Desciption" value="{{ old('description') }}" required>

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-tag"></i><label class="ms-2">Unit Price</label>
                                </span>
                                <input id="unit_price" type="text" class="form-control @error('unit_price') is-invalid @enderror" name="unit_price" placeholder="Format Sample: unit_price" value="{{ old('unit_price') }}" required>
                                <small class="form-text text-danger mt-2" style="color: red">
                                    Note: Please enter a whole number like 5999.
                                </small>
                                @error('unit_price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-scale-balanced"></i><label class="ms-2">Unit of Measure</label>
                                </span>
                                <input id="UoM" type="text" class="form-control @error('UoM') is-invalid @enderror" name="UoM" placeholder="UoM" value="{{ old('UoM') }}" required>

                                @error('UoM')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-warehouse"></i><label class="ms-2">Quantity in Stock</label>
                                </span>
                                <input id="quantity_in_stock" type="text" class="form-control @error('quantity_in_stock') is-invalid @enderror" name="quantity_in_stock" placeholder="quantity_in_stock" value="{{ old('quantity_in_stock') }}" required>
                                @error('quantity_in_stock')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-warehouse"></i><label class="ms-2">Reorder Level</label>
                                </span>
                                <input id="reorder_level" type="text" class="form-control @error('reorder_level') is-invalid @enderror" name="reorder_level" placeholder="reorder_level" value="{{ old('reorder_level') }}" required>
                                @error('reorder_level')
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
