@extends('layouts.app')
@include('common.navbar')

@section('content')
<style>
    /* Styling */
    .card { background-color: #34495e; border: none; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
    .card-header { background-color: #2c3e50; font-size: 1.5rem; padding: 15px; }
    .form-label { font-weight: bold; color: #ecf0f1; }
    .product-entry { background-color: #2c3e50; padding: 15px; border-radius: 8px; margin-bottom: 15px; color: #ecf0f1; }
    .product-info p { margin: 0; }
    .btn-primary { background-color: #2980b9; border: none; }
    .btn-secondary { background-color: #27ae60; border: none; }
    .btn-primary:hover, .btn-secondary:hover { opacity: 0.8; }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center text-white">{{ __('Create Sales') }}</div>
                <div class="card-body">
                    @include('common.alert')

                    <form id="sales-form" method="POST" action="{{ url('sales') }}">
                        @csrf
                        <div id="product-fields">
                            <div class="product-entry">
                                <label for="product_id" class="form-label">{{ __('Product ID') }}</label>
                                <input type="text" class="form-control product_id" name="product_id[]" pattern="^\d{8}$" required>
                                <div class="product-info" style="display: none;">
                                    <p><strong>Product Name:</strong> <span class="product_name"></span></p>
                                    <p><strong>Color:</strong> <span class="color"></span></p>
                                    <p><strong>Size:</strong> <span class="size"></span></p>
                                    <p><strong>Description:</strong> <span class="description"></span></p>
                                    <p><strong>Category:</strong> <span class="product_category"></span></p>
                                    <p><strong>Seller:</strong> <span class="product_seller"></span></p>
                                    <p><strong>In Stock:</strong> <span class="product_stock"></span></p>
                                    <p><strong>Store Stock:</strong> <span class="store_stock"></span></p>
                                    <p><strong>Stockroom Stock:</strong> <span class="stockroom_stock"></span></p>
                                    <p class="price-info" style="display: none;"><strong>Price per Unit: ₱</strong><span class="sales_price"></span></p>
                                    <p class="unit-info" style="display: none;"><strong>Unit: </strong><span class="unit"></span></p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <label for="quantity" class="form-label">{{ __('Quantity') }}</label>
                                        <input type="number" class="form-control quantity" name="quantity[]" pattern="^\d{1,6}$" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="total_amount" class="form-label">{{ __('Amount') }}</label>
                                        <input type="text" class="form-control total_amount" name="total_amount[]" readonly>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                        
                        <label for="grand_total_amount" class="form-label">{{ __('Total Amount') }}</label>
                        <input type="text" class="form-control" id="grand_total_amount" name="grand_total_amount" readonly>

                        <button type="button" id="add-another-product" class="btn btn-secondary mb-3">{{ __('Add Another Product') }}</button>
                        <button type="submit" class="btn btn-primary" style="margin-left: 65%;">{{ __('Submit') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('sales-form').addEventListener('blur', function(e) {
        if (e.target.classList.contains('product_id')) {
            const productId = e.target.value;
            if (productId) {
                fetchProductDetails(productId, e.target);
            }
        }
    }, true);

    document.getElementById('add-another-product').addEventListener('click', function() {
    const newProductEntry = document.createElement('div');
    newProductEntry.className = 'product-entry';
    newProductEntry.innerHTML = `
        <label for="product_id" class="form-label">{{ __('Product ID') }}</label>
        <input type="text" class="form-control product_id" name="product_id[]" required>
        <div class="product-info" style="display: none;">
            <p><strong>Product Name:</strong> <span class="product_name"></span></p>
            <p><strong>Color:</strong> <span class="color"></span></p>
            <p><strong>Size:</strong> <span class="size"></span></p>
            <p><strong>Description:</strong> <span class="description"></span></p>
            <p><strong>Category:</strong> <span class="product_category"></span></p>
            <p><strong>Seller:</strong> <span class="product_seller"></span></p>
            <p><strong>In Stock:</strong> <span class="product_stock"></span></p>
            <p><strong>Store Stock:</strong> <span class="store_stock"></span></p>
            <p><strong>Stockroom Stock:</strong> <span class="stockroom_stock"></span></p>
            <p class="price-info" style="display: none;"><strong>Price per Unit: ₱</strong><span class="sales_price"></span></p>
            <p class="unit-info" style="display: none;"><strong>Unit: </strong><span class="unit"></span></p>
        </div>
        <div class="row mt-3">
            <div class="col-6">
                <label for="quantity" class="form-label">{{ __('Quantity') }}</label>
                <input type="number" class="form-control quantity" name="quantity[]" required>
            </div>
            <div class="col-6">
                <label for="total_amount" class="form-label">{{ __('Amount') }}</label>
                <input type="text" class="form-control total_amount" name="total_amount[]" readonly>
            </div>
        </div>
        <hr>
    `;

    // Append the new product entry to the product fields container
    document.getElementById('product-fields').appendChild(newProductEntry);

    // Add an event listener for the new product ID input to validate its format
    const productIdInput = newProductEntry.querySelector('.product_id');
    productIdInput.addEventListener('input', function() {
        const value = this.value;
        // Regular expression to check for 8 digits
        const isValid = /^\d{8}$/.test(value);

        if (!isValid && value.length > 0) {
            this.setCustomValidity("Product ID must be exactly 8 digits.");
            this.reportValidity(); // Display validation message
        } else {
            this.setCustomValidity(""); // Reset error message
        }
    });
});


    function fetchProductDetails(productId, inputField) {
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
                const productInfoDiv = inputField.closest('.product-entry').querySelector('.product-info');
                productInfoDiv.style.display = 'block';
                
                productInfoDiv.querySelector('.product_name').innerText = data.product.product_name || 'N/A';
                productInfoDiv.querySelector('.color').innerText = data.product.descriptionArray.color || 'N/A';
                productInfoDiv.querySelector('.size').innerText = data.product.descriptionArray.size || 'N/A';
                productInfoDiv.querySelector('.description').innerText = data.product.descriptionArray.description || 'N/A';
                productInfoDiv.querySelector('.product_category').innerText = data.product.category_name || 'N/A';
                productInfoDiv.querySelector('.product_seller').innerText = data.seller || 'N/A';
                productInfoDiv.querySelector('.product_stock').innerText = data.product.in_stock || 'Out of Stock';

                let storeStock = data.product.in_stock - data.product.product_quantity;
                productInfoDiv.querySelector('.store_stock').innerText = storeStock > 0 ? storeStock : 'Out of Stock';
                productInfoDiv.querySelector('.stockroom_stock').innerText = data.product.product_quantity || 'Out of Stock';

                const priceInfo = productInfoDiv.querySelector('.price-info');
                const unitInfo = productInfoDiv.querySelector('.unit-info');

                // Show or hide based on stock availability
                if (data.product.in_stock > 0) {
                    priceInfo.querySelector('.sales_price').innerText = data.product.sale_price_per_unit;
                    unitInfo.querySelector('.unit').innerText = data.product.unit_of_measure;
                    priceInfo.style.display = 'block';
                    unitInfo.style.display = 'block';

                    const quantityInput = inputField.closest('.product-entry').querySelector('.quantity');
                    quantityInput.addEventListener('input', function() {
                        const totalAmountInput = inputField.closest('.product-entry').querySelector('.total_amount');
                        totalAmountInput.value = (this.value * data.product.sale_price_per_unit).toFixed(2);
                        calculateGrandTotal();
                    });
                } else {
                    priceInfo.style.display = 'none';
                    unitInfo.style.display = 'none';
                }

                const price = data.product.sale_price_per_unit;
                const quantityInput = inputField.closest('.product-entry').querySelector('.quantity');
                const totalAmountInput = inputField.closest('.product-entry').querySelector('.total_amount');
                quantityInput.addEventListener('input', function() {
                    totalAmountInput.value = (this.value * price).toFixed(2);
                    calculateGrandTotal();
                });
            } else {
                alert(data.message);
            }
        });
    }

    function calculateGrandTotal() {
        const totalAmountFields = document.querySelectorAll('.total_amount');
        let grandTotal = 0;
        totalAmountFields.forEach(field => {
            grandTotal += parseFloat(field.value) || 0;
        });
        document.getElementById('grand_total_amount').value = grandTotal.toFixed(2);
    }
</script>
@endsection