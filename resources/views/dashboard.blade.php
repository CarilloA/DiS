@extends('layouts.app')
<!-- Include the vertical navigation bar -->
@include('common.navbar')

<style>
    body {
        background-image: url('/storage/images/bg-photo.jpeg');
        background-size: cover; /* Cover the entire viewport */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat; /* Prevent the image from repeating */
    }

    /* Main content styling */
    .content {
        margin-right: 250px; /* Leave space for the sidebar on larger screens */
        padding: 20px;
        overflow: hidden; /* Prevent content overflow */
        transition: margin-right 0.3s; /* Smooth transition when sidebar toggles */
        position: relative; /* Ensure relative positioning for overlays */
        z-index: 1; /* Ensure content is above background */
    }

    /* Ensure that the card does not overflow horizontally 
    .card {
        max-width: 100%;
        overflow: hidden;
        color: #fff !important;
        background-color: #565656 !important; 
        border-radius: 8px; 
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }*/

    .main-content {
        padding: 20px; /* Add padding for inner spacing */
        margin: 0 20px; /* Add left and right margin */
        color: #fff !important;
        background-color: #565656 !important; 
        border-radius: 5px; /* Slightly rounded corners */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }

    @media (max-width: 768px) {
        .content {
            margin-right: 0; /* Remove margin on smaller screens */
        }
    }
</style>

@section('content')
    <div class="content"> <!-- Add the content class to prevent overlap -->
        @if(Auth::user()->role == "Administrator")
            <div class="container">
                <!-- Alert Messages -->
                @include('common.alert')
                <div class="row justify-content-center">
                    <div class="col">
                        <div class="main-content">
                            <!--<div class="text card-header text-center text-light fw-bold" style="background-color: #3a8f66">
                                {{ __("DASHBOARD: EMPLOYEE ACCOUNT LIST") }}
                            </div>-->
                            <h1 class="text-center mt-4 mb-4">Admin Dashboard</h1>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(Auth::user()->role == "Inventory Manager")
            <div class="container">
                <!-- Alert Messages -->
                @include('common.alert')
                <div class="row justify-content-center">
                    <div class="col">
                        <div class="main-content">
                            <!--<div class="text card-header text-center text-light fw-bold" style="background-color: #3a8f66">
                                {{ __("DASHBOARD: EMPLOYEE ACCOUNT LIST") }}
                            </div>-->
                            <!-- Check and display low stock messages -->
                            @if(!empty($lowStockMessages))
                                <div class="alert alert-warning">
                                    <strong>Low Stock Alerts:</strong>
                                    <ul>
                                        @foreach($lowStockMessages as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <h1 class="text-center">Inventory Manager Dashboard</h1>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(Auth::user()->role == "Auditor")
            <div class="container">
                <!-- Alert Messages -->
                @include('common.alert')
                <div class="row justify-content-center">
                    <div class="col">
                        <div class="main-content">
                            <!--<div class="text card-header text-center text-light fw-bold" style="background-color: #3a8f66">
                                {{ __("DASHBOARD: EMPLOYEE ACCOUNT LIST") }}
                            </div>-->
                            <h1 class="text-center">Auditor Dashboard</h1>
                        </div>
                    </div>
                </div>
            </div>
        @endif 
    </div>
@endsection
