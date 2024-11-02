@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
@if(Auth::user()->role == "Administrator") <!-- Check if user is an administrator -->
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <!-- Alert Messages -->
                @include('common.alert')
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                    <h1 class="h2">Account Management</h1>
                </div>

                <!-- Table Section -->
                <table class="table table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>
                                <a class="text-black d-block py-2 px-4" href="{{ route('account_management.create') }}">+ Add New User</a>
                            </th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>User Role</th>
                            <th>Action</th>
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
                                    <td>{{ $data->role }}</td>
                                    <td>
                                        <!-- Trigger the modal with a button -->
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $data->user_id }}">
                                            Delete
                                        </button>
                                    </td>

                                    <!-- Modal -->
                                    <div id="deleteModal{{ $data->user_id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Confirm Deletion</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
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