@extends('layouts.app')

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

            {{-- Form for submitting all inventory data --}}
            <form action="{{ route('inventory.audit.step2') }}" method="POST">
                @csrf
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Count Quantity on Hand</th>
                            <th>Count Stock in the Store</th>
                            <th>Count Stock in the Stockroom</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventoryJoined as $data)
                            <tr>
                                <td>{{ $data->product_name }}</td>
                                
                                <input type="hidden" name="inventory_id[]" value="{{ $data->inventory_id }}">
                                <input type="hidden" name="stockroom_id[]" value="{{ $data->stockroom_id }}">
                                <input type="hidden" name="previous_quantity_on_hand[]" value="{{ $data->in_stock }}">

                                <td>
                                    <div class="form-group">
                                        <label for="count_quantity_on_hand">Count Quantity on Hand</label>
                                        <input class="form-control" type="number" name="count_quantity_on_hand[]" placeholder="Input number only" pattern="^\d{1,6}$" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="count_store_quantity">Count Stock in the Store</label>
                                        <input class="form-control" type="number" name="count_store_quantity[]" placeholder="Input number only" pattern="^\d{1,6}$" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="count_stockroom_quantity">Count Stock in the Stockroom</label>
                                        <input class="form-control" type="number" name="count_stockroom_quantity[]" placeholder="Input number only" pattern="^\d{1,6}$" required>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No inventory found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Next</button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
