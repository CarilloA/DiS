@extends('layouts.app')

@section('content')
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
        border: none !important; /* Remove border */
    }

    input:focus {
        background-color: #2c2f32; /* Change to a slightly different color on focus */
        color: white; /* Ensure text color is white */
        outline: none; /* Remove the default focus outline */
        border: 2px solid #1abc9c; /* Optional: Add border to highlight focus */
    }

    .card {
        border: none; 
        min-height: 400px;
        background-color: #565656; 
        backdrop-filter: blur(10px); 
        width: 1500px; 
        max-width: 90%; 
        height: 100%; 
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }

    .card-body {
        display: flex; 
        align-items: stretch; 
        justify-content: space-between;
        padding: 40px;
        border-radius: 15px;
    }

    .loginIMG {
        width: 40%; 
        height: 100%; 
        object-fit: cover;  
        border-radius: 10px;
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
        transition: background-color 0.3s ease; 
    }

    .btn-login {
        background-color: #3a8f66; 
        color: white; 
        border: none;
    }

    .btn-login:hover {
        background-color: #2f6b5a; 
    }

    .btn-register {
        background-color: #231f20; 
        color: white; 
        border: none;
        transition: background-color 0.3s ease; 
    }

    .btn-register:hover {
        background-color: #0d0d0d;
    }

    .forgot-password {
        margin-right: 0px;
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

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <!-- Left Side: Login Image -->
                    <img src="/storage/images/loginIMG.png" class="loginIMG img-fluid" alt="login.jpg">

                    <!-- Right Side: Login Form -->
                    <div class="login-form w-50" style="color: white; margin-top: 20px;">
                        <!-- Alert Messages -->
                        @include('common.alert')
                        <!-- Logo Image -->
                        <div class="text-center mb-4">
                            <img src="/storage/images/DiS_Logo.png" class="img-fluid" alt="logo" style="width: 25vw; height: auto; background: transparent;">
                        </div>
                        <div style="">
                            <p class="mt-8" style="color: #fff">Login to your account</p>
                        
                            <!-- Only show login form if no roles are available -->
                            @if (empty($roles))
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <!-- Email Input -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input id="email" type="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class="fa fa-user fa-lg"></i>
                                        </span>
                                    </div>
                                    @error('email')
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
                                    <div class="ms-auto"> 
                                        @if (Route::has('password.request'))
                                            <a class="forgot-password btn btn-link" href="{{ route('register_account.create') }}" style="color: #fff;">
                                                {{ __('Register') }}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="ms-auto"> 
                                        <a class="createAccount btn btn-link" href="{{ route('password.request') }}" style="color: #fff;">
                                            {{ __('Forgot Password?') }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Login Button -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-login mb-4">
                                        {{ __('Login') }}
                                    </button>
                                </div>
                            </form>
                            @endif
                        
                            <!-- Only show role selection form if roles are available -->
                            @if (!empty($roles))
                            <form method="POST" action="{{ route('select-role') }}">
                                @csrf
                                
                                <!-- Confirm Password Input -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input id="password" type="password" placeholder="Confirm Password" class="form-control" name="password" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="role" style="color: #fff;">Choose a role to login:</label>
                                    <select name="role" id="role" class="form-control" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-login mb-4">
                                        {{ __('Login with Role') }}
                                    </button>
                                </div>
                            </form>
                            @endif
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
