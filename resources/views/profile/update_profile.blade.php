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
                <div class="card-header text-center">{{ __('Update User Account') }}</div>
                <div class="card-body">
                    @include('common.alert')
                    <form method="POST" action="{{ url('update_profile/' . $user->user_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture -->
                        <div class="form-group mb-3">
                            <label for="">{{ __('Choose Profile Picture') }}</label>
                            <input type="file" name="image_url" class="form-control" accept="image/*">
                        </div>

                        <!-- First and Last Name -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa fa-user fa-lg"></i><label class="ms-2">First Name</label>
                                </span>
                                <input id="first_name" type="text" class="form-control" name="first_name" value="{{ $user->first_name }}">
                            </div>

                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa fa-user fa-lg"></i><label class="ms-2">Last Name</label>
                                </span>
                                <input id="last_name" type="text" class="form-control" name="last_name" value="{{ $user->last_name }}">
                            </div>
                        </div>

                        <!-- Email and Mobile Number -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-envelope fa-lg"></i><label class="ms-2">Email Address</label>
                                </span>
                                <input id="email" type="email" class="form-control" name="email" value="{{ $user->contact->email ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-sim-card fa-lg"></i><label class="ms-2">Mobile Number</label>
                                </span>
                                <input id="mobile_number" type="number" class="form-control" name="mobile_number" value="{{ $user->contact->mobile_number ?? '' }}">
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="input-group-text">
                                    <i class="fa fa-user fa-lg"></i><label class="ms-2">Username</label>
                                </span>
                                <input id="username" type="text" class="form-control" name="username" value="{{ $user->credential->username ?? '' }}">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" name="update">
                                    {{ __('Update Profile') }}
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
