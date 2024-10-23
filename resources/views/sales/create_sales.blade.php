@extends('layouts.app')
@include('common.navbar')

@section('content')
<style>
    .card {
        background-color: #34495e; 
        border: none; 
        border-radius: 8px; 
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center text-white">{{ __('Create Sales') }}</div>
                <div class="card-body">
                    @include('common.alert')

                    <form method="POST" action="{{ url('sales') }}">
                        @csrf

                        <!-- Product ID Input -->
                        <div class="form-group mb-3">
                            <label for="product_id">{{ __('Product ID') }}</label>
                            <input type="text" class="form-control" id="product_id" name="product_id" required>
                        </div>

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

                        <!-- Quantity Input -->
                        <div class="form-group mb-3">
                            <label for="quantity">{{ __('Quantity') }}</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>

                        <!-- Total Amount -->
                        <div class="form-group mb-3">
                            <label for="total_amount">{{ __('Total Amount') }}</label>
                            <input type="text" class="form-control" id="total_amount" name="total_amount" readonly>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" name="create" class="btn btn-primary">{{ __('Submit') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('product_id').addEventListener('blur', function() {
        const productId = this.value;
        if (productId) {
            fetchProductDetails(productId);
        }
    });

    document.getElementById('quantity').addEventListener('input', function() {
        const quantity = this.value;
        const pricePerUnit = document.getElementById('product_stock').getAttribute('data-price');
        if (pricePerUnit && quantity) {
            document.getElementById('total_amount').value = quantity * pricePerUnit;
        }
    });

    function fetchProductDetails(productId) {
        fetch('{{ route('fetch.product') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('product-info').style.display = 'block';
                document.getElementById('product_name').innerText = data.product.product_name;

                // Handle description array
                const descriptionArray = data.product.descriptionArray || {};
                document.getElementById('color').innerText = descriptionArray.color || 'N/A';
                document.getElementById('size').innerText = descriptionArray.size || 'N/A';
                document.getElementById('description').innerText = descriptionArray.description || 'N/A';

                document.getElementById('product_category').innerText = data.product.category_name;
                document.getElementById('product_seller').innerText = data.seller;
                document.getElementById('product_stock').innerText = data.product.in_stock;

                // Only show price and unit if the product is in stock
                if (data.product.in_stock > 0) {
                    document.getElementById('price-info').style.display = 'block';
                    document.getElementById('unit-info').style.display = 'block';
                    document.getElementById('sales_price').innerText = data.product.sale_price_per_unit;
                    document.getElementById('unit').innerText = data.product.unit_of_measure;

                    // Set data attributes for further calculation
                    document.getElementById('product_stock').setAttribute('data-price', data.product.sale_price_per_unit);
                    document.getElementById('product_stock').setAttribute('data-unit', data.product.unit_of_measure);
                } else {
                    document.getElementById('price-info').style.display = 'none';
                    document.getElementById('unit-info').style.display = 'none';
                    document.getElementById('product_stock').setAttribute('data-price', '');
                }
            } else {
                alert(data.message);
            }
        });
    }
</script>
@endsection
