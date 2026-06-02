@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="card" style="margin-bottom:18px">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:14px;align-items:end;flex-wrap:wrap">
            <div class="field mb0">
                <label>From</label>
                <input type="date" name="from" value="{{ \Illuminate\Support\Carbon::parse($from)->format('Y-m-d') }}">
            </div>
            <div class="field mb0">
                <label>To</label>
                <input type="date" name="to" value="{{ \Illuminate\Support\Carbon::parse($to)->format('Y-m-d') }}">
            </div>
            <button class="btn primary">Apply</button>
            <button class="btn ghost no-print" type="button" onclick="window.print()">🖨 Print</button>
        </form>
    </div>
</div>

<div class="grid cols-3">
    <div class="card stat"><div class="icon">💰</div><div class="label">Total Sales</div><div class="value">${{ number_format($totalsales, 2) }}</div></div>
    <div class="card stat"><div class="icon">🧾</div><div class="label">Orders</div><div class="value">{{ $ordercount }}</div></div>
    <div class="card stat"><div class="icon">📊</div><div class="label">Average Order</div><div class="value">${{ number_format($average, 2) }}</div></div>
</div>

<div class="grid cols-2" style="margin-top:18px">
    <div class="card">
        <div class="card-head"><h3>Sales by Payment Method</h3></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Method</th><th class="num">Orders</th><th class="num">Total</th></tr></thead>
                <tbody>
                @forelse($bymethod as $m)
                    <tr><td>{{ ucfirst($m->method) }}</td><td class="num">{{ $m->orders }}</td><td class="num">${{ number_format($m->total, 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="muted">No data in this range.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h3>Best Selling Products</h3></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Product</th><th class="num">Qty Sold</th><th class="num">Revenue</th></tr></thead>
                <tbody>
                @forelse($topproducts as $tp)
                    <tr><td>{{ $tp->name }}</td><td class="num">{{ $tp->sold }}</td><td class="num">${{ number_format($tp->earned, 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="muted">No data in this range.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card" style="margin-top:18px">
    <div class="card-head"><h3>Daily Breakdown</h3></div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Date</th><th class="num">Orders</th><th class="num">Total</th></tr></thead>
            <tbody>
            @forelse($byday as $d)
                <tr><td>{{ \Illuminate\Support\Carbon::parse($d->day)->format('d M Y') }}</td><td class="num">{{ $d->orders }}</td><td class="num">${{ number_format($d->total, 2) }}</td></tr>
            @empty
                <tr><td colspan="3" class="muted">No sales in this range.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
