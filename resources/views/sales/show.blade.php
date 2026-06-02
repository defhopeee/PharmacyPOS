@extends('layouts.app')
@section('title', 'Sale ' . $sale->reference)

@section('content')
<div class="card" style="max-width:760px">
    <div class="card-head">
        <h3>{{ $sale->reference }}</h3>
        <a class="btn primary sm" href="{{ route('sales.receipt', $sale) }}" target="_blank"><x-icon name="printer" size="16" /> Print Receipt</a>
    </div>
    <div class="card-body">
        <div class="grid cols-2" style="margin-bottom:18px">
            <div>
                <p class="mb0 muted">Attendant</p><strong>{{ $sale->user->name ?? '—' }}</strong>
                <p class="mb0 muted" style="margin-top:10px">Customer</p><strong>{{ $sale->customer ?? 'Walk-in' }}</strong>
            </div>
            <div>
                <p class="mb0 muted">Date</p><strong>{{ $sale->createdat->format('d M Y, H:i') }}</strong>
                <p class="mb0 muted" style="margin-top:10px">Payment Method</p><span class="badge teal">{{ ucfirst($sale->method) }}</span>
            </div>
        </div>

        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Item</th><th class="num">Price</th><th class="num">Qty</th><th class="num">Total</th></tr></thead>
                <tbody>
                @foreach($sale->items as $it)
                    <tr><td>{{ $it->name }}</td><td class="num">${{ number_format($it->price, 2) }}</td><td class="num">{{ $it->quantity }}</td><td class="num">${{ number_format($it->total, 2) }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div style="max-width:280px;margin-left:auto;margin-top:18px">
            <div class="sumrow"><span class="muted">Subtotal</span><span>${{ number_format($sale->subtotal, 2) }}</span></div>
            <div class="sumrow"><span class="muted">Discount</span><span>-${{ number_format($sale->discount, 2) }}</span></div>
            <div class="sumrow"><span class="muted">Tax</span><span>${{ number_format($sale->tax, 2) }}</span></div>
            <div class="sumrow total"><span>Total</span><span>${{ number_format($sale->total, 2) }}</span></div>
            <div class="sumrow"><span class="muted">Paid</span><span>${{ number_format($sale->paid, 2) }}</span></div>
            <div class="sumrow"><span class="muted">Change</span><span>${{ number_format($sale->balance, 2) }}</span></div>
        </div>

        <a class="btn ghost" href="{{ route('sales.index') }}" style="margin-top:18px">← Back to sales</a>
    </div>
</div>
@endsection
