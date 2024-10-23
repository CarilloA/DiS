<h2>Product: {{ $product_name }}</h2>
<p>Total Refund Amount: ${{ $total_refund }}</p>

<form action="/refund-exchange" method="POST">
    @csrf
    <button name="action" value="refund">Refund</button>
    <button name="action" value="exchange">Exchange</button>
</form>
