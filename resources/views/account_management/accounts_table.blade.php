@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
@if(Auth::user()->credential->role == "Administrator") <!-- Check if user is an administrator -->
    <div class="container-fluid">
        <div class="row">

            <!-- Main Content -->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
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
                            <th colspan="2" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($userJoined) > 0) <!-- Check if userJoined is not null and not empty -->
                            @foreach($userJoined as $data)
                                <tr>
                                    <td>{{ $data->first_name }}</td>
                                    <td>{{ $data->last_name }}</td>
                                    <td>{{ $data->username }}</td>
                                    <td>{{ $data->email }}</td>
                                    <td>{{ $data->mobile_number }}</td>
                                    <td>{{ $data->role }}</td>
                                    <td colspan="2">
                                        <a href="" style="color: #1abc9c"><i class="fa-solid fa-user-pen fa-lg"></i></a>
                                        <a href="" style="color: maroon"><i class="fa-solid fa-user-slash fa-lg"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">No Inventory Managers found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </main>
        </div>
    </div>
@endif
@endsection
