@extends('layouts.app')
<!-- Include the vertical navigation bar -->
@include('common.navbar')

<style>
    /* Main content styling */
    .content {
        margin-right: 250px; /* Leave space for the sidebar on larger screens */
        padding: 20px;
        overflow: hidden; /* Prevent content overflow */
        transition: margin-right 0.3s; /* Smooth transition when sidebar toggles */
    }

    /* Ensure that the card does not overflow horizontally */
    .card {
        max-width: 100%;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .content {
            margin-right: 0; /* Remove margin on smaller screens */
        }
    }
</style>

@section('content')
<body>
    <div class="content"> <!-- Add the content class to prevent overlap -->
        @if(Auth::user()->role == "Administrator")
        <div class="container">
            <!-- Alert Messages -->
            @include('common.alert')
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="text card-header text-center text-light fw-bold" style="background-color: rgb(154, 243, 144)">
                            {{ __("DASHBOARD: EMPLOYEE ACCOUNT LIST") }}
                        </div>
                        <hr style="margin-top: -5px;">
                        <h1 class="text-center">Welcome Admin, dashboard here</h1>
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
                    <div class="card">
                        <div class="text card-header text-center text-light fw-bold" style="background-color: rgb(154, 243, 144)">
                            {{ __("DASHBOARD: EMPLOYEE ACCOUNT LIST") }}
                        </div>
                        <hr style="margin-top: -5px;">
                        <h1 class="text-center">Welcome Inventory Manager, dashboard here</h1>
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
                    <div class="card">
                        <div class="text card-header text-center text-light fw-bold" style="background-color: rgb(154, 243, 144)">
                            {{ __("DASHBOARD: EMPLOYEE ACCOUNT LIST") }}
                        </div>
                        <hr style="margin-top: -5px;">
                        <h1 class="text-center">Welcome Auditor, dashboard here</h1>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</body>
@endsection
