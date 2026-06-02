<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Receipt {{ $sale->reference }}</title>
    <link rel="stylesheet" href="{{ asset_v('css/app.css') }}">
</head>
<body style="background:#f1f5f9">
<div class="receipt">
    <h2><x-icon name="cross" size="18" /> {{ config('app.name') }}</h2>
    <p class="r-center">Your trusted neighbourhood pharmacy<br>Tel: +000 000 0000</p>
    <hr>
    <div class="r-line"><span>Receipt</span><span>{{ $sale->reference }}</span></div>
    <div class="r-line"><span>Date</span><span>{{ $sale->createdat->format('d M Y H:i') }}</span></div>
    <div class="r-line"><span>Served by</span><span>{{ $sale->user->name ?? '—' }}</span></div>
    <hr>
    @foreach($sale->items as $it)
        <div class="r-line"><span>{{ $it->name }} x{{ $it->quantity }}</span><span>{{ money($it->total) }}</span></div>
    @endforeach
    <hr>
    <div class="r-line" style="font-weight:800;font-size:1rem"><span>TOTAL</span><span>{{ money($sale->total) }}</span></div>
    <div class="r-line"><span>Paid ({{ strtoupper($sale->method) }})</span><span>{{ money($sale->paid) }}</span></div>
    @if($sale->mpesareceipt)<div class="r-line"><span>M-Pesa Ref</span><span>{{ $sale->mpesareceipt }}</span></div>@endif
    <div class="r-line"><span>Change</span><span>{{ money($sale->balance) }}</span></div>
    <hr>
    <p class="r-center">Thank you for your purchase!<br>Get well soon. <x-icon name="heart" size="13" /></p>
    <div class="no-print" style="text-align:center;margin-top:18px">
        <button class="btn primary sm" onclick="window.print()"><x-icon name="printer" size="15" /> Print</button>
        <button class="btn ghost sm" onclick="window.close()">Close</button>
    </div>
</div>
</body>
</html>
