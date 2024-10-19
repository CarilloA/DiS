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
        display: flex; 
        justify-content: center; 
        align-items: center; 
    }

    .form-control::placeholder {
        color: white;
    }

    .form-control {
        background-color: #565656;
        color: white;
        border: 0.8px solid white; 
        border-radius: .25rem; 
        font-family: Arial, sans-serif;
    }

    input::placeholder {
        color: white;
        font-weight: 100;
    }

    input {
        background-color: #212529;
        color: white;
        border: 2px solid white; 
        font-family: Arial, sans-serif;
    }

    .card {
        border: none; 
        min-height: 400px;
        background-color: #565656; 
        backdrop-filter: blur(10px); 
        width: 1500px; 
        max-width: 90%; 
        height: 100%; /* Set height to allow children to expand */
    }

    .card-body {
        display: flex; 
        border: 2px solid #79fabd; 
        align-items: stretch; 
        justify-content: space-between;
        padding: 40px;
    }

    .loginIMG {
        width: 40%; 
        height: 100%; /* Make the image fill the entire height of the card */
        object-fit: cover; /* Ensure the image covers the space without distortion */
    }

    .alert {
        margin-bottom: 1.5rem;
        text-align: center;
        font-weight: bold;
    }

    .input-group {
        position: relative; 
    }

    .input-group .form-control {
        padding-right: 40px; 
        background-color: #212529; 
        color: white; 
        border: 0.8px solid white; 
        border-radius: .25rem; 
        font-family: Arial, sans-serif;
    }

    .input-group .input-group-text {
        position: absolute; 
        right: 10px; 
        top: 50%; 
        transform: translateY(-50%); 
        background-color: transparent; 
        border: none; 
        color: white; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }

    .btn {
        transition: background-color 0.3s ease; /* Smooth transition for hover effect */
    }

    .btn-login {
        background-color: #3a8f66; 
        color: white; 
        border: none;
    }

    .btn-login:hover {
        background-color: #2f6b5a; /* Darkens the button */
    }

    .btn-register {
        background-color: #231f20; 
        color: white; 
        border: none;
        transition: background-color 0.3s ease; /* Smooth transition for hover effect */
    }

    .btn-register:hover {
        background-color: #0d0d0d; /* Darker hover color */
    }

    @media (max-width: 768px) {
        .card-body {
            flex-direction: column; 
            align-items: center; 
        }

        .loginIMG {
            width: 100%; 
            margin-bottom: 20px; 
        }

        .login-form {
            width: 100%; 
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
                    <img src="/storage/images/loginIMG.png" class="loginIMG img-fluid" alt="login.jpg">

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
                                <div class="ms-auto"> <!-- Added this div for right alignment -->
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}" style="color: #fff;">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Login Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-login">
                                    {{ __('Login') }}
                                </button>
                            </div>

                            <!-- Register Button with Gap -->
                            <div class="d-grid gap-2" style="margin-top: 10px;"> 
                                <a class="btn btn-register" href="{{ route('register') }}">
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
