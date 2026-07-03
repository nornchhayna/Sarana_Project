<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
</head>
<body>

<h1>Invoice #{{ $invoice->invoice_number }}</h1>

<p>Customer: {{ $invoice->order->user->name }}</p>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
    </tr>

    @foreach($invoice->order->items as $item)
    <tr>
        <td>{{ $item->product_name }}</td>
        <td>{{ $item->quantity }}</td>
        <td>${{ number_format($item->unit_price, 2) }}</td>
        <td>${{ number_format($item->subtotal, 2) }}</td>
    </tr>
    @endforeach
</table>

<h3>Total: ${{ number_format($invoice->order->total, 2) }}</h3>

</body>
</html>