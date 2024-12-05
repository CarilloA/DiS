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

    /*Delete Icon*/
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

    /* filter button style */
    .filter-button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        position: relative; /* For positioning the notification circle */
    }

    .filter-button:hover {
        background-color: #0056b3;
    }

    /* Positioning for the notification circle inside <th> */
        th {
        position: relative; /* Make <th> the reference point for absolute positioning */
    }

    /* Notification Circle Style */
    .notification-circle {
        position: absolute;
        top: 0px; /* Adjust the top position if necessary */
        right: 25px; /* Adjust the right position if necessary */
        background-color: #dc3545; /* Red background for the circle */
        color: white;
        width: 20px; /* Circle width */
        height: 20px; /* Circle height */
        border-radius: 50%; /* Make it a circle */
        text-align: center;
        font-size: 12px;
        line-height: 20px; /* Center the number inside the circle */
    }


    /* Dropdown Styles */
    .dropdown-menu {
        min-width: 200px;
    }

    /* Flexbox styling for the filter button and banner */
    .d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
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

                     <!-- Dropdown with Buttons -->
                     <div class="row d-flex justify-content-end"> <!-- Align the row to the right -->
                        <div class="col-auto"> <!-- Display All button in dropdown -->
                            <div class="dropdown">
                                <button class="filter-button dropdown-toggle" type="button" id="displayAllDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Display Options
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="displayAllDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('accounts_table') }}">
                                            Display All
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('accounts_table.confirm_reject_filter') }}">
                                            Confirm/Reject Account
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('accounts_table.resend_link_filter') }}">
                                            Resend Verification Link
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    
                    

                    <!-- Table Section -->
                    <table class="table table-responsive table-hover">
                        
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>User Role</th>
                                <th>Email Verified At</th>
                                <th>
                                    Resend Link
                                    @if($pendingResendLinkCount >= 0)
                                        <div class="notification-circle">
                                            {{ $pendingResendLinkCount }}
                                        </div>
                                    @endif
                                </th>
                                <th colspan="2">
                                    Confirm User Login
                                    @if($pendingConfirmRejectCount >= 0)
                                        <div class="notification-circle">
                                            {{ $pendingConfirmRejectCount }}
                                        </div>
                                    @endif
                                </th>
                                <th>Delete Account</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($userSQL) > 0) <!-- Check if userSQL is not null and not empty -->
                                @foreach($userSQL as $data)
                                    <tr>
                                        <td>{{ $data->first_name }}</td>
                                        <td>{{ $data->last_name }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->mobile_number ?? 'Unassigned' }}</td>
                                        <td>{{ $data->user_roles ?? 'Unassigned' }}</td>
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
                                            <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal{{ $data->user_id }}">Confirm</button></td>
                                            <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal{{ $data->user_id }}">Reject</button></td>
                                        @else
                                            <td><button type="button" class="btn btn-primary" disabled>Confirm</button></td>
                                            <td><button type="button" class="btn btn-danger" disabled>Reject</button></td>
                                        @endif
                                        <td>
                                            <!-- Trigger the modal with a button -->
                                            <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#deleteModal{{ $data->user_id }}" title="Delete">
                                                <div class="circle-icon" title="Delete">
                                                    <i class="bi bi-person-x" style="font-size: 1rem;"></i>
                                                </div>
                                            </button>
                                        </td>

                                        {{-- reject modal --}}
                                        <div id="rejectModal{{ $data->user_id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Reject User Login</h4>
                                                        <button type="button" class="close custom-close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('reject_account', $data->user_id) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $data->user_id }}">
                                                            <input type="hidden" name="action" value="reject">
                                                            
                                                            <!-- Admin Password Input -->
                                                            <div class="form-group">
                                                                <label for="admin_password">Admin Password*</label>
                                                                <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                                                       id="admin_password_{{ $data->user_id }}" name="admin_password" required>
                                                                <small class="form-text text-light mt-2">
                                                                    Note: Please enter your current password for confirmation.
                                                                </small>
                                                                @error('admin_password')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-danger">Reject User Login</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        

                                        {{-- confirm madal --}}
                                        <div id="confirmModal{{ $data->user_id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirm User Login</h4>
                                                        <button type="button" class="close custom-close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('confirm_account', $data->user_id) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $data->user_id }}">
                                                            <input type="hidden" name="action" value="confirm">
                                                            
                                                            <!-- Admin Password Input -->
                                                            <div class="form-group">
                                                                <label for="admin_password">Admin Password*</label>
                                                                <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                                                       id="admin_password_{{ $data->user_id }}" name="admin_password" required>
                                                                <small class="form-text text-light mt-2">
                                                                    Note: Please enter your current password for confirmation.
                                                                </small>
                                                                @error('admin_password')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <!-- Roles Selection -->
                                                            <div class="form-group">
                                                                <label>Select User Roles:</label>
                                                                <div class="form-check">
                                                                    <input id="inventory_manager" type="checkbox" class="form-check-input @error('roles') is-invalid @enderror" 
                                                                           name="roles[]" value="Inventory Manager">
                                                                    <label for="inventory_manager" class="form-check-label">Inventory Manager</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input id="auditor" type="checkbox" class="form-check-input @error('roles') is-invalid @enderror" 
                                                                           name="roles[]" value="Auditor">
                                                                    <label for="auditor" class="form-check-label">Auditor</label>
                                                                </div>
                                                                @error('roles')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Confirm User Login</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        

                                        <!-- Delete Modal -->
                                        <div id="deleteModal{{ $data->user_id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirm Deletion</h4>
                                                        <button type="button" class="close custom-close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('account_management.destroy', $data->user_id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            
                                                            <input type="hidden" name="user_id" value="{{ $data->user_id }}">
                                                            <input type="hidden" name="action" value="delete">
                                                            
                                                            <!-- Admin Password Input -->
                                                            <div class="form-group">
                                                                <label for="admin_password">Admin Password*</label>
                                                                <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                                                       id="admin_password_{{ $data->user_id }}" name="admin_password" required>
                                                                <small class="form-text text-light mt-2">
                                                                    Note: Please enter your current password for confirmation.
                                                                </small>
                                                                @error('admin_password')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            
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
                                    <td colspan="10" class="text-center">No user found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
@endif

<script>
    $(document).ready(function () {
        // Dynamically open the correct modal based on validation errors
        @if ($errors->any())
            let userId = '{{ old("user_id") }}'; // Retrieve the user_id from the old input
            let action = '{{ old("action") }}'; // Retrieve the action from the old input

            if (action === 'reject') {
                $('#rejectModal' + userId).modal('show');
            } else if (action === 'confirm') {
                $('#confirmModal' + userId).modal('show');
            } else if (action === 'delete') {
                $('#deleteModal' + userId).modal('show');
            }
        @endif
    });
</script>


@endsection