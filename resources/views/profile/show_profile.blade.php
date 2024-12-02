@extends('layouts.app')
@include('common.navbar')

@section('content')

<style>
    body {
        background-image: url('/storage/images/bg-photo.jpeg');
        background-size: cover; /* Cover the entire viewport */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat; /* Prevent the image from repeating */
    }

    /* Main content styling */
    .main-content {
        padding: 20px; /* Add padding for inner spacing */
        margin: 0 20px; /* Add left and right margin */
        background-color: #565656; /* Light background for contrast */
        border-radius: 5px; /* Slightly rounded corners */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }

    h1.h2 {
        color: #fff; /* Change this to your desired color */
    }

    .card-header {
        background-color: #3a8f66; /* Blue background for headers */
        color: white; /* White text for headers */
        font-weight: bold; /* Bold text for headers */
    }

    .card-body {
        padding: 1.5rem; /* Padding inside the card body */
    }

    .text-center {
        text-align: center; /* Center text in the header and body */
    }

    /* Responsive image styling */
    .profile-pic {
        width: 100%; /* Full width */
        height: auto; /* Maintain aspect ratio */
        max-height: 150px; /* Maximum height for uniformity */
        object-fit: cover; /* Cover the space without distortion */
        border-radius: 5px; /* Optional: rounded corners for the image */
    }

    .profile-pic {
        width: 100px; /* Fixed width for the circular image */
        height: 100px; /* Fixed height for the circular image */
        object-fit: cover; /* Cover the space without distortion */
        border-radius: 50%; /* Make the image circular */
        border: 2px solid #1abc9c; /* Optional: add a border to the circle */
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

<div class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
    <div class="main-content">
        
        @include('common.alert')

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
            <h1 class="h2 mb-4 mt-2">Profile Management</h1>
        </div>
        <div class="row">
            <div class="col-md-4">
                <!-- User Profile Card -->
                <div class="card mb-4 shadow-sm" style="height: 15rem; display: flex; flex-direction: column; justify-content: space-between;">
                    <div class="text-center mt-3">
                        <img src="{{ asset('storage/userImage/' . Auth::user()->image_url) }}" 
                            alt="Profile Picture" 
                            class="img-fluid profile-pic">
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title font-weight-bold">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h5>
                        <a href="{{ url('edit_profile/'. Auth::user()->user_id) }}" class="btn" style="background-color: #3a8f66; color: #fff;">Edit Profile</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Credentials Card -->
                <div class="card mb-4 shadow-sm" style="height: 15rem;">
                    <h5 class="card-header text-center">My Credentials</h5>
                    <div class="card-body text-center">
                        <ul class="list-group list-group-flush text-center">
                            <li class="list-group-item">Role: <strong>{{ Auth::user()->role }}</strong></li>
                            <li class="list-group-item">Username: <strong>{{ Auth::user()->username }}</strong></li>
                            <li class="list-group-item">Password: <strong>********</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Contact Details Card -->
                <div class="card mb-4 shadow-sm" style="height: 15rem;">
                    <h5 class="card-header text-center">My Contact Details</h5>
                    <ul class="list-group list-group-flush text-center">
                        <li class="list-group-item">Mobile Number: <strong>{{ Auth::user()->mobile_number }}</strong></li>
                        <li class="list-group-item">Email: <strong>{{ Auth::user()->email }}</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>   
</div>

@endsection
