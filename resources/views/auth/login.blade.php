@extends('layouts.app')

@push('styles')
    <style>
        .form-control::placeholder {
            color: white;
        }

        .form-control {
            background-color: #212529;
            color: white;
            border: none;
        }

        /* For other inputs in case */
        input::placeholder {
            color: white;
        }

        input {
            background-color: #212529;
            color: white;
            border: none;
        }

        /* Make the image height match the card's height */
        .card {
            border: none; 
            min-height: 400px; /* Ensure card has a minimum height */
        }

        .card-body {
            display: flex; 
            border: 2px solid #79fabd; 
            align-items: stretch; /* Ensure elements fill the card body height */
            justify-content: space-between;
        }
    </style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="border: none; background-color: #d7eae1;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <!-- Left Side: Login Image -->
                    <img src="/storage/images/loginIMG.png" class="loginIMG img-fluid" alt="login.jpg" style="width: 40%; height: auto; object-fit: cover;">

                    <!-- Right Side: Login Form -->
                    <div class="login-form w-50" style="color: white;">
                        <!-- Logo Image -->
                        <div class="text-center mb-4">
                            <img src="/storage/images/DiS_Logo.png" class="img-fluid" alt="logo" style="width: 25vw; height: auto; background: transparent;">
                        </div>
                        <p class="mt-8" style="color: #0f5132">Login to your account</p> 
                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Username Input -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="background-color: #0f5132; border: none; color: white;">
                                        <i class="fa fa-user fa-lg"></i>
                                    </span>
                                    <input id="username" type="text" placeholder="Username" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username">
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
                                    <span class="input-group-text" id="basic-addon1" style="background-color: #0f5132; border: none; color: white;">
                                        <i class="fa fa-key fa-lg"></i>
                                    </span>
                                    <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
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
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="background-color: #212529; border-color: #0f5132;">
                                    <label class="form-check-label" for="remember" style="color: #0f5132;">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}" style="color: #0f5132;">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>

                            <!-- Login Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn" style="background-color: #0f5132; color: white; border: none;">
                                    {{ __('Login') }}
                                </button>
                            </div>

                            <!-- Register Button -->
                            <div class="d-grid gap-2">
                                <a class="btn btn-link" href="{{ route('register') }}" style="color: #0f5132;">
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
