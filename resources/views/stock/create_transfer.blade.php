@extends('layouts.app')
@include('common.navbar')

@section('content')
    <form action="{{ route('stock_transfer.store') }}" method="POST">
        @csrf
        <div>
            <label for="from_stockroom_id">From Stockroom</label>
            <select name="from_stockroom_id" required>
                @foreach($stockrooms as $stockroom)
                    <option value="{{ $stockroom->stockroom_id }}">{{ $stockroom->aisle_number }} - {{ $stockroom->cabinet_level }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="to_stockroom_id">To Stockroom</label>
            <select name="to_stockroom_id" required>
                @foreach($stockrooms as $stockroom)
                    <option value="{{ $stockroom->stockroom_id }}">{{ $stockroom->aisle_number }} - {{ $stockroom->cabinet_level }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="product_id">Product</label>
            <select name="product_id" required>
                @foreach($products as $product)
                    <option value="{{ $product->product_id }}">{{ $product->product_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="transfer_quantity">Quantity</label>
            <input type="number" name="transfer_quantity" required min="1">
        </div>

        <button type="submit">Transfer</button>
    </form>
@endsection
