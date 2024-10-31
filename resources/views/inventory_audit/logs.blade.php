@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
    <table class="table table-responsive">
        <thead>
            <tr>
                <th>Product</th>
                <th>Auditor</th>
                <th>Previous Quantity</th>
                <th>New Quantity</th>
                <th>Reason</th>
                <th>Audit Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($auditLogs as $log)
                <div>
                    <tr>
                        <td>{{ $log->inventory->product->product_name }}</td>
                        <td>{{ $log->user->username }}</td>
                        <td>{{ $log->previous_quantity }}</td>
                        <td>{{ $log->new_quantity }}</td>
                        <td>{{ $log->reason }}</td>
                        <td>{{ $log->audit_date }}</td>
                    </tr>
                </div>
            @endforeach
        </tbody>
    </table>
@endsection
