@extends('layouts.app')
@include('common.navbar')

@section('content')


<form action="return_product" method="POST">
    @csrf
    <label for="sales_id">Sales ID</label>
    <input type="text" name="sales_id" required>
    
    <!-- Product Information Display -->
    <div id="product-info" class="mb-3" style="display: none;">
        <p><strong>Product Name:</strong> <span id="product_name"></span></p>
        <p><strong>Color:</strong> <span id="color"></span></p>
        <p><strong>Size:</strong> <span id="size"></span></p>
        <p><strong>Description:</strong> <span id="description"></span></p>
        <p><strong>Category:</strong> <span id="product_category"></span></p>
        <p><strong>Seller:</strong> <span id="product_seller"></span></p>
        <p><strong>In Stock:</strong> <span id="product_stock"></span></p>
        <p id="price-info" style="display: none;"><strong>Price per Unit: ₱</strong><span id="sales_price"></span></p>
        <p id="unit-info" style="display: none;"><strong>Unit: </strong><span id="unit"></span></p>
    </div>
    
    <label for="quantity">Quantity</label>
    <input type="number" name="quantity" min="1" required>

    <label for="reason">Reason for Returning</label>
    <textarea name="reason" required></textarea>

    <button type="submit">Proceed</button>
</form>
@endsection

