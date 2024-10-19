@extends('layouts.app')

@push('styles')
<style>
    body {
        background-image: url('/storage/images/bg-photo.jpeg'); 
        background-size: cover; 
        background-position: center; 
        background-repeat: no-repeat; 
        height: 100vh; 
        margin: 0; 
        display: flex; /* Use flexbox for centering */
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically */
    }

    .form-control::placeholder {
        color: white;
    }

    .form-control {
        background-color: #565656;
        color: white;
        border: 0.px solid white; /* Set border weight here */
        border-radius: .25rem; /* Rounded corners */
    }

    input::placeholder {
        color: white;
        font-weight: 100;
    }

    input {
        background-color: #212529;
        color: white;
        border: 2px solid white; /* Set border weight here */
    }

    .card {
        border: none; 
        min-height: 400px;
        background-color: #565656; /* Semi-transparent card background */
        backdrop-filter: blur(10px); /* Optional: Adds a blur effect behind the card */
        width: 1500px; /* Fixed width for the card */
        max-width: 90%; /* Ensure it doesn't exceed 90% on small screens */
    }

    .card-body {
        display: flex; 
        border: 2px solid #79fabd; 
        align-items: stretch; 
        justify-content: space-between;
    }

    .alert {
        margin-bottom: 1.5rem;
        text-align: center;
        font-weight: bold;
    }

    .input-group {
        position: relative; /* Make the input group relative for absolute positioning */
    }

    .input-group .form-control {
        padding-right: 40px; /* Add padding to the right for the icon */
        background-color: #212529; /* Input background color */
        color: white; /* Text color */
        border: 0.8px solid white; /* Set border weight */
        border-radius: .25rem; /* Rounded corners */
    }

    .input-group .input-group-text {
        position: absolute; /* Position absolutely within the input group */
        right: 10px; /* Position the icon to the right */
        top: 50%; /* Center vertically */
        transform: translateY(-50%); /* Adjust vertical alignment */
        background-color: transparent; /* Make background transparent */
        border: none; /* Remove border */
        color: white; /* Icon color */
        display: flex; /* Flexbox to center the icon */
        align-items: center; /* Center the icon vertically */
        justify-content: center; /* Center the icon horizontally */
    }

    @media (max-width: 768px) {
        .card-body {
            flex-direction: column; /* Stack elements on smaller screens */
            align-items: center; /* Center align elements */
        }

        .loginIMG {
            width: 100%; /* Full width for image on small screens */
            margin-bottom: 20px; /* Space between image and form */
        }

        .login-form {
            width: 100%; /* Full width for form on small screens */
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <!-- Left Side: Login Image -->
                    <img src="/storage/images/loginIMG.png" class="loginIMG img-fluid" alt="login.jpg" style="width: 40%; height: auto; object-fit: cover;">

                    <!-- Right Side: Login Form -->
                    <div class="login-form w-50" style="color: white;">
                        <!-- Alert Messages -->
                        @include('common.alert')
                        <!-- Logo Image -->
                        <div class="text-center mb-4">
                            <img src="/storage/images/DiS_Logo.png" class="img-fluid" alt="logo" style="width: 25vw; height: auto; background: transparent;">
                        </div>
                        <p class="mt-8" style="color: #fff">Login to your account</p>
                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Username Input -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <input id="username" type="text" placeholder="Username" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="fa fa-user fa-lg"></i>
                                    </span>
                                    </input>
                                </div>
                                @error('username')
                                    <span class="invalid-feedback" role="alert" style="color: #dc3545;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Password Input -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                    <span class="input-group-text" id="basic-addon2">
                                        <i class="fa fa-key fa-lg"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert" style="color: #dc3545;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="border: 2px solid white;">
                                    <label class="form-check-label" for="remember" style="color: #fff;">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}" style="color: #fff;">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>

                            <!-- Login Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn" style="background-color: #3a8f66; color: white; border: none;">
                                    {{ __('Login') }}
                                </button>
                            </div>

                            <!-- Register Button -->
                            <div class="d-grid gap-2">
                                <a class="btn btn-link" href="{{ route('register') }}" style="color: #fff;">
                                    {{ __('Register') }}
                                </a>
                            </div>
                        </form>
                    </div> <!-- End of Login Form -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
