@extends('layouts.app')

@section('content')
<style>
    body {
        background-image: url('/storage/images/bg-photo.jpeg');
        background-size: cover; /* Cover the entire viewport */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat; /* Prevent the image from repeating */
        background-color: #1a1a1a; /* Dark background */
        color: #fff; /* Light text color */
        height: 100vh; /* Full viewport height */
        display: flex; /* Enable flexbox */
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically */
        margin: 0; /* Remove default margin */
    }
    .card {
        background-color: #565656; /* Card background */
        border: none; /* Remove border */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
        width: 100%;
        max-width: 700px; /* Constrain the card width */
    }
    .input-group-text {
        background-color: #3a8f66; /* Input group background */
        border: none; /* Remove borders */
        color: #fff; /* White text */
    }
    .btn-primary {
        background-color: #3a8f66; /* Green button */
        color: #fff; /* White text */
        border: none; /* Remove button borders */
    }
    .btn-primary:hover {
        background-color: #2f6b5a; /* Darker green on hover */
    }
    .btn-secondary {
        background-color: #3a8f66; /* Dark background for role selection */
        color: #fff; /* White text */
        border: none;
    }
    .btn-secondary:hover {
        background-color: #2f6b5a; /* Darker green on hover */
    }

    /* check box role styels */
    .role-button {
        display: inline-block;
        padding: 10px 20px;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        background-color: #f8f9fa;
        color: #000;
        transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        user-select: none;
    }

    .role-button.active {
        background-color: #28a745;
        color: #fff;
        border-color: #28a745;
    }

    .role-button:hover {
        background-color: #e9ecef;
    }

    .d-none {
        display: none;
    }

</style>

<div class="card">
    <div class="card-header text-center" style="background-color:#3a8f66; color:#fff; font-weight: bold;">{{ __('Register Admin Account') }}</div>
    <div class="card-body">
        <!-- Alert Messages -->
        @include('common.alert')
        <form method="POST" action="{{ route('admin.register.submit') }}" enctype="multipart/form-data">
            @csrf

            <h6 class="text text-light mb-4">
                <strong>Important: </strong> Complete all fields in the registration form carefully. All input fields w/ asterisk (*) are required for successful registration.
            </h6>

            <!-- Input fields -->
            <div class="row mb-3">
                <div class="col">
                    <span class="input-group-text">
                        <i class="fa fa-user fa-lg"></i><label class="ms-2">First Name*</label>
                    </span>
                    <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" placeholder="Format Sample: Gabriel" value="{{ old('first_name') }}" pattern="^[A-Z]{1}[a-z]*$" required>
                    <small class="text form-text text-light mt-2">
                        Note: Please enter the value starting with an uppercase letter, followed by lowercase letters only.
                    </small>
                    @error('first_name')
                        <span class="invalid-feedback" role="alert" style="color: pink;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col">
                    <span class="input-group-text">
                        <i class="fa fa-user fa-lg"></i><label class="ms-2">Last Name*</label>
                    </span>
                    <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" placeholder="Format Sample: Madriago" value="{{ old('last_name') }}" pattern="^[A-Z]{1}[a-z]*$" required>
                    <small class="text form-text text-light mt-2">
                        Note: Please enter the value starting with an uppercase letter, followed by lowercase letters only.
                    </small>
                    @error('last_name')
                        <span class="invalid-feedback" role="alert" style="color: pink;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col mb-4">
                    <span class="input-group-text">
                        <i class="fa-solid fa-envelope fa-lg"></i><label class="ms-2">Email Address*</label>
                    </span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email Address should be valid" value="{{ old('email') }}" required>
                    <small class="text form-text text-light mt-2">
                        Note: Please enter a verified email address.
                    </small>
                    @error('email')
                        <span class="invalid-feedback" role="alert" style="color: pink;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="below">
                    <div class="row mb-3">
                        <label class="text" for="roles" class="col-md-4 col-form-label text-md-end" style="color: #fff;">
                            {{ __('Select Additional User Roles (Optional):') }}
                        </label>
                        <div class="col mb-4">
                            <div class="button-group">
                                <div class="role-button" data-role="Administrator" style="display: none;">
                                    <input id="administrator" type="checkbox" class="form-check-input d-none" name="roles[]" value="Administrator" checked> <!-- Set the 'checked' attribute here -->
                                    <label for="administrator" class="form-check-label">{{ __('Administrator') }}</label>
                                </div>
                                <div class="role-button" data-role="Inventory Manager">
                                    <input id="inventory_manager" type="checkbox" class="form-check-input d-none" name="roles[]" value="Inventory Manager">
                                    <label for="inventory_manager" class="form-check-label">{{ __('Inventory Manager') }}</label>
                                </div>
                                <div class="role-button" data-role="Auditor">
                                    <input id="auditor" type="checkbox" class="form-check-input d-none" name="roles[]" value="Auditor">
                                    <label for="auditor" class="form-check-label">{{ __('Auditor') }}</label>
                                </div>
                            </div>
                            <span class="invalid-feedback" role="alert" style="display: none; color: #fff;">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                
                
                <div class="row mb-3">
                    <div class="col">
                        <span class="input-group-text">
                            <i class="fa fa-key fa-lg"></i><label class="ms-2">Password*</label>
                        </span>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Must be a strong password" pattern="^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*_\-\\\.\+]).{8,}$" required>
                        <small class="text form-text text-light mt-2">
                            Note: Please enter at least 8 characters with a number, symbol, capital letter, and small letter.
                        </small>
                        @error('password')
                            <span class="invalid-feedback" role="alert" style="color: pink;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col">
                        <span class="input-group-text">
                            <i class="fa fa-key fa-lg"></i><label class="ms-2">Confirm Password*</label>
                        </span>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Entered password should match." required>
                    </div>
                </div>
            </div>

            <div class="row mb-0">
                <div class="col text-center">
                    <button type="submit" name="create" class="btn btn-primary">
                        {{ __('Register') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

    <script>
        // document.addEventListener("DOMContentLoaded", function () {
        //     const roleButtons = document.querySelectorAll(".role-button");

        //     roleButtons.forEach(button => {
        //         button.addEventListener("click", function () {
        //             const input = this.querySelector("input");
        //             const isChecked = input.checked;

        //             // Toggle checkbox state
        //             input.checked = !isChecked;

        //             // Toggle active class
        //             this.classList.toggle("active", input.checked);
        //         });
        //     });
        // });

        document.addEventListener("DOMContentLoaded", function () {
            const roleButtons = document.querySelectorAll(".role-button");

            // Automatically select the Administrator role on page load
            const adminButton = document.querySelector('[data-role="Administrator"]');
            const adminCheckbox = adminButton.querySelector('input');
            adminCheckbox.checked = true; // Ensure the Administrator role is checked by default
            adminButton.classList.add("active"); // Add the 'active' class to the button for visual indication

            roleButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const input = this.querySelector("input");
                    const isChecked = input.checked;

                    // Toggle checkbox state
                    input.checked = !isChecked;

                    // Toggle active class
                    this.classList.toggle("active", input.checked);
                });
            });
        });


        // for error handling
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("form");
            const checkboxes = document.querySelectorAll('input[name="roles[]"]');
            const errorSpan = document.querySelector(".invalid-feedback");
            
            form.addEventListener("submit", function (event) {
                let checked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                if (!checked) {
                    event.preventDefault();
                    errorSpan.textContent = "Please select at least one role.";
                    errorSpan.classList.add("d-block"); // Ensure the error message is visible
                } else {
                    errorSpan.textContent = "";
                    errorSpan.classList.remove("d-block");
                }
            });

            // Remove error when a checkbox is selected
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", function () {
                    if (Array.from(checkboxes).some(c => c.checked)) {
                        errorSpan.textContent = "";
                        errorSpan.classList.remove("d-block");
                    }
                });
            });
        });
    </script>
@endsection

