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

    .table th, td {
        background-color: #565656 !important; /* Set background color for all table headers */
        color: #ffffff !important;
    }

    /* Styling for input boxes */
    .modal-content input {
        background-color: #212529;
        font-family: Arial, sans-serif;
        border: none; /* Remove border */
        color: #fff;
    }

    .modal-content input:focus {
        background-color: #212529; /* Maintain the same background color */
        color: white; /* Ensure text color is white */
        outline: none; /* Remove the default focus outline */
    }
    
    /*Icon*/
    .circle-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px; /* Set the width of the circle */
        height: 30px; /* Set the height of the circle */
        border-radius: 50%; /* Makes it a circle */
        background-color: #dc3545; /* Light red background */
        color: white; /* Icon color */
        font-size: 1.5rem; /* Adjust icon size */
        transition: background-color 0.3s; /* Smooth transition for background color */
    }

    .circle-icon:hover {
        background-color: #c82333; /* Darker red on hover */
    }

    .modal-content{
        background-color:#565656 !important;
        color: #fff !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }

    /*close button in modal*/
    .custom-close {
        background-color: transparent; /* Make background transparent */
        color: white; /* Keep text color white */
        border: none; /* Remove border */
        font-size: 24px; /* Adjust size if needed */
        cursor: pointer; /* Change cursor to pointer */
        padding: 0; /* Remove padding */
        outline: none; /* Remove outline on focus */
    }

    .custom-close:hover {
        color: #ccc; /* Optional: change color on hover */
    }
</style>

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
@endpush
@if(Auth::user()->role == "Administrator") <!-- Check if user is an administrator -->
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="main-content">
                    @include('common.alert')
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2">Account Management</h1>
                    </div>

                    <a class="btn btn-success mt-3 mb-3" href="{{ route('account_management.create') }}">+ Add New User</a>

                    <!-- Table Section -->
                    <table class="table table-responsive table-hover">
                        
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>User Role</th>
                                <th>Email Verified At</th>
                                <th>Resend Link</th>
                                <th>Confirm User Login</th>
                                <th>Delete Account</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($userSQL) > 0) <!-- Check if userSQL is not null and not empty -->
                                @foreach($userSQL as $data)
                                    <tr>
                                        <td>{{ $data->first_name }}</td>
                                        <td>{{ $data->last_name }}</td>
                                        <td>{{ $data->username }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->mobile_number }}</td>
                                        <td>{{ $data->user_roles }}</td>
                                        @if($data->email_verified_at !=null)
                                            <td>{{ $data->email_verified_at }}</td>
                                            <td><button type="button" class="btn btn-primary" disabled>Resend Link</button></td>
                                        @else
                                            <td>Not Yet Verified</td>
                                            <td>
                                                <form action="{{ route('resend_confirmation_email', $data->user_id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">Resend Link</button>
                                                </form>
                                            </td>
                                        @endif
                                        @if($data->user_roles ===null)
                                            <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal{{ $data->user_id }}">Confirm Login</button></td>
                                        @else
                                            <td><button type="button" class="btn btn-primary" disabled>Confirm Login</button></td>
                                        @endif
                                        <td>
                                            <!-- Trigger the modal with a button -->
                                            <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#deleteModal{{ $data->user_id }}" title="Delete">
                                                <div class="circle-icon" title="Delete">
                                                    <i class="bi bi-person-x" style="font-size: 1rem;"></i>
                                                </div>
                                            </button>
                                        </td>

                                        <div id="confirmModal{{ $data->user_id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirm User Account</h4>
                                                        <button type="button" class="close custom-close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('confirm_account', $data->user_id) }}" method="POST">
                                                            @csrf
                                        
                                                            {{-- Identify which modal to open to display error alert --}}
                                                            <input type="hidden" name="user_id" value="{{ $data->user_id }}">
                                        
                                                            <div class="below">
                                                                <div class="row mb-3">
                                                                    <label class="text" for="roles" class="col-md-4 col-form-label text-md-end">{{ __('Select User Roles:') }}</label>
                                                                    <div class="col-md-6">
                                                                        <div class="form-check">
                                                                            <input id="inventory_manager" type="checkbox" class="form-check-input @error('roles') is-invalid @enderror" name="roles[]" value="Inventory Manager">
                                                                            <label for="inventory_manager" class="form-check-label">{{ __('Inventory Manager') }}</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input id="auditor" type="checkbox" class="form-check-input @error('roles') is-invalid @enderror" name="roles[]" value="Auditor">
                                                                            <label for="auditor" class="form-check-label">{{ __('Auditor') }}</label>
                                                                        </div>
                                                                        @error('roles')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                        
                                                            <!-- Modal Validation Error Alert Message -->
                                                            @if ($errors->any() && old('user_id') == $data->user_id)
                                                                <div class="alert alert-danger">
                                                                    <ul>
                                                                        @foreach ($errors->all() as $error)
                                                                            <li>{{ $error }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                                <script>
                                                                    $(document).ready(function() {
                                                                        $('#confirmModal{{ $data->user_id }}').modal('show');
                                                                    });
                                                                </script>
                                                            @endif
                                        
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Confirm User Account</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        

                                        <!-- Delete Modal -->
                                        <div id="deleteModal{{ $data->user_id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirm Deletion</h4>
                                                        <button type="button" class="close custom-close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('account_management.destroy', $data->user_id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')

                                                            {{-- to identofy which modal to open to display error alert --}}
                                                            <input type="hidden" name="user_id" value="{{ $data->user_id }}">

                                                            <!-- Admin Username Input -->
                                                            <div class="form-group">
                                                                <label for="admin_username">Admin Username</label>
                                                                <input type="text" class="form-control" id="admin_username_{{ $data->user_id }}" name="admin_username" required>
                                                            </div>

                                                            <!-- Admin Password Input -->
                                                            <div class="form-group">
                                                                <label for="admin_password">Admin Password</label>
                                                                <input type="password" class="form-control" id="admin_password_{{ $data->user_id }}" name="admin_password" required>
                                                            </div>

                                                            <!-- Modal Validation Error Alert Message-->
                                                            @if ($errors->any() && old('user_id') == $data->user_id)
                                                                <div class="alert alert-danger">
                                                                    <ul>
                                                                        @foreach ($errors->all() as $error)
                                                                            <li>{{ $error }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                                <script>
                                                                    $(document).ready(function() {
                                                                        $('#deleteModal{{ $data->user_id }}').modal('show');
                                                                    });
                                                                </script>
                                                            @endif

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No user found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- Alert Messages -->
                
            </main>
        </div>
    </div>
@endif
@endsection

<!-- JavaScript to clear the input fields when the modal is closed -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function(){
        // Loop through each delete modal
        @foreach($userSQL as $data)
        $('#deleteModal{{ $data->user_id }}').on('hidden.bs.modal', function () {
            // Clear input fields
            $('#admin_username_{{ $data->user_id }}').val('');
            $('#admin_password_{{ $data->user_id }}').val('');
        });
        @endforeach
    });
</script>