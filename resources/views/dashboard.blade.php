@extends('layouts.app')
<!-- Include the vertical navigation bar -->
@include('common.navbar')

<style>
/* Main content styling */
.content {
    margin-right: 250px; /* Leave space for the sidebar on the right */
    padding: 20px;
    overflow: hidden; /* Prevent content overflow */
}

/* Responsive: Hide the sidebar on smaller screens */
@media (max-width: 768px) {
    #sidebar {
        display: none;
    }

    .toggle-btn {
        display: block;
    }

    .content {
        margin-right: 0; /* Remove margin on smaller screens */
    }
}

/* Ensure that the card does not overflow horizontally */
.card {
    max-width: 100%;
    overflow: hidden;
}

/* Toggle button for small screens */
.toggle-btn {
    display: none;
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 30px;
    color: white;
    cursor: pointer;
}
</style>

@section('content')
<body>
    <div class="content"> <!-- Add the content class to prevent overlap -->
        @if(Auth::user()->credential->role == "Administrator") <!-- checks if user is an administrator -->
        <div class="container">
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

        @if(Auth::user()->credential->role == "Inventory Manager") <!-- checks if user is an inventory manager -->
        <div class="container">
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

        @if(Auth::user()->credential->role == "Auditor") <!-- checks if user is an inventory manager -->
        <div class="container">
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