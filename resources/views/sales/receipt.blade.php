<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Receipt {{ $sale->reference }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background:#f1f5f9">
<div class="receipt">
    <h2>💊 {{ config('app.name') }}</h2>
    <p class="r-center">Your trusted neighbourhood pharmacy<br>Tel: +000 000 0000</p>
    <hr>
    <div class="r-line"><span>Receipt</span><span>{{ $sale->reference }}</span></div>
    <div class="r-line"><span>Date</span><span>{{ $sale->createdat->format('d M Y H:i') }}</span></div>
    <div class="r-line"><span>Served by</span><span>{{ $sale->user->name ?? '—' }}</span></div>
    <div class="r-line"><span>Customer</span><span>{{ $sale->customer ?? 'Walk-in' }}</span></div>
    <hr>
    @foreach($sale->items as $it)
        <div class="r-line"><span>{{ $it->name }} x{{ $it->quantity }}</span><span>${{ number_format($it->total, 2) }}</span></div>
    @endforeach
    <hr>
    <div class="r-line"><span>Subtotal</span><span>${{ number_format($sale->subtotal, 2) }}</span></div>
    <div class="r-line"><span>Discount</span><span>-${{ number_format($sale->discount, 2) }}</span></div>
    <div class="r-line"><span>Tax</span><span>${{ number_format($sale->tax, 2) }}</span></div>
    <div class="r-line" style="font-weight:800;font-size:1rem"><span>TOTAL</span><span>${{ number_format($sale->total, 2) }}</span></div>
    <div class="r-line"><span>Paid ({{ ucfirst($sale->method) }})</span><span>${{ number_format($sale->paid, 2) }}</span></div>
    <div class="r-line"><span>Change</span><span>${{ number_format($sale->balance, 2) }}</span></div>
    <hr>
    <p class="r-center">Thank you for your purchase!<br>Get well soon. 💚</p>
    <div class="no-print" style="text-align:center;margin-top:18px">
        <button class="btn primary sm" onclick="window.print()">🖨 Print</button>
        <button class="btn ghost sm" onclick="window.close()">Close</button>
    </div>
</div>
</body>
</html>
