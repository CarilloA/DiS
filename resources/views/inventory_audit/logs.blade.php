@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <!-- Alert Messages -->
            @include('common.alert')
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Auditor</th>
                        <th>Previous QoH</th>
                        <th>New Store Stock</th>
                        <th>New Stockroom Stock</th>
                        <th>New QoH</th>
                        <th>Variance</th>
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
                                <td>{{ $log->previous_quantity_on_hand }}</td>
                                <td>{{ $log->new_store_quantity }}</td>
                                <td>{{ $log->new_stockroom_quantity }}</td>
                                <td>{{ $log->new_quantity_on_hand }}</td>
                                <td>{{ $log->variance }}</td>
                                <td>{{ $log->reason }}</td>
                                <td>{{ $log->audit_date }}</td>
                            </tr>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </main>
    </div>
@endsection
