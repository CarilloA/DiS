@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
<div class="container-fluid">
    <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="main-content">
            <!-- Alert Messages -->
            @include('common.alert')
            {{-- Progress bar at the top --}}
            <div class="progress" style="height: 20px; margin-bottom: 20px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                     style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                     {{ $progress }}%
                </div>
            </div>

            {{-- if no discrepancies found on product stocks display this --}}
            @if($discrepancies == null) 
                <h1>NO PRODUCT STOCK DESCREPANCY FOUND</h1>
                <button type="button" class="btn btn-success" onclick="window.location.href='{{ route('audit_inventory_table') }}'">
                    Go Back
                </button>
            @else
                {{-- Display all the inventory products with discrepancy based on the inputted data from step 1 --}}
                <form action="{{ route('inventory.audit.step3') }}" method="POST">
                    @csrf
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Counted Quantity on Hand</th>
                                <th>Counted Stock in the Store</th>
                                <th>Counted Stock in the Stockroom</th>
                                <th>Discrepancy (QoH)</th>
                                <th>Discrepancy (Store Stock)</th>
                                <th>Discrepancy (Stockroom Stock)</th>
                                <th>Reason for Discrepancy</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discrepancies as $key => $discrepancy)
            
                                <input type="hidden" name="inventory_id[]" value="{{ $discrepancy['inventory']->inventory_id }}">
                                <tr>
                                    {{ $discrepancy['inventory']->product_name }}</td> 
                                    <td>{{ $discrepancy['inventory']->product_name }}</td> 
                                    <td>{{ $discrepancy['fetch_quantity_on_hand'] }}</td> <!-- Fetch the inputted count_quantity_on_hand -->
                                    <td>{{ $discrepancy['fetch_store_quantity'] }}</td> <!-- Fetch the inputted count_store_quantity -->
                                    <td>{{ $discrepancy['fetch_stockroom_quantity'] }}</td> <!-- Fetch the inputted count_stockroom_quantity -->

                                    <td>{{ $discrepancy['variance_in_stock'] }}</td>
                                    <td>{{ $discrepancy['variance_store_stock'] }}</td>
                                    <td>{{ $discrepancy['variance_stockroom_quantity'] }}</td>
                                    <td><input type="text" name="reason[{{ $key }}]" required></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Next</button>
                </form>
            @endif
        </div>
    </main>
</div>
@endsection
