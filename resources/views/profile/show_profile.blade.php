@extends('layouts.app')
@include('common.navbar')

<style>
.card {
        border-radius: 10px; /* Rounded corners for cards */
        border: none; /* Remove default card border */
        box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;
    }
</style>

@section('content')

<style>
    .card-header {
        background-color: #1abc9c; /* Blue background for headers */
        color: white; /* White text for headers */
        font-weight: bold; /* Bold text for headers */
    }
    .card-body {
        padding: 1.5rem; /* Padding inside the card body */
    }
    .text-center {
        text-align: center; /* Center text in the header and body */
    }
    @media (max-width: 768px) {
        .container {
            padding: 0 15px; /* Responsive padding on smaller screens */
        }
        .col-md-4, .col-md-8 {
            margin-bottom: 15px; /* Add spacing between cards on small screens */
        }
    }
</style>

<div class="container mt-5">
    <div class="row">
        <div class="col">
            <!-- User Profile Card -->
            <div class="card mb-4 shadow-sm" style="width: 20rem; height: 33rem;">
                <div class="text-center mt-3 mb-3">
                    <img src="{{ asset('storage/userImage/' . Auth::user()->image_url) }}" 
                         alt="Profile Picture" 
                         class="img-fluid profile-pic" 
                         style="width: 450px; height: 280px;">
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title font-weight-bold">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h5>
                    <a href="{{ url('edit_profile/'. Auth::user()->user_id) }}" class="btn" style="background-color: #1abc9c">Edit Profile</a>
                </div>
            </div>
        </div>

        <div class="col">
        <!-- Credentials Card -->
            <div class="card mb-4 shadow-sm" style="width: 18rem; height: 15rem;">
                <h5 class="card-header text-center">My Credentials</h5>
                <div class="card-body text-center">
                    <ul class="list-group list-group-flush text-center">
                        <li class="list-group-item">Username: <strong>{{ Auth::user()->credential->username }}</strong></li>
                        <li class="list-group-item">Role: <strong>{{ Auth::user()->credential->role }}</strong></li>
                    </ul>
                </div>
            </div>

            <!-- Contact Details Card -->
            <div class="card mb-4 shadow-sm" style="width: 18rem; height: 15rem;">
                <h5 class="card-header text-center">My Contact Details</h5>
                <ul class="list-group list-group-flush text-center">
                    <li class="list-group-item">Mobile Number: <strong>{{ Auth::user()->contact->mobile_number }}</strong></li>
                    <li class="list-group-item">Email: <strong>{{ Auth::user()->contact->email }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection