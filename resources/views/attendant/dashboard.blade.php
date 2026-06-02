@extends('layouts.app')
@section('title', 'Attendant Dashboard')

@section('content')
<div class="grid cols-3">
    <div class="card stat">
        <div class="iconbox"><x-icon name="dollar" size="22" /></div>
        <div class="label">My Sales Today</div>
        <div class="value">${{ number_format($mysalestoday, 2) }}</div>
        <div class="trend">{{ $myorderstoday }} orders processed</div>
    </div>
    <div class="card stat">
        <div class="iconbox"><x-icon name="receipt" size="22" /></div>
        <div class="label">Orders Today</div>
        <div class="value">{{ $myorderstoday }}</div>
        <div class="trend">Keep it up!</div>
    </div>
    <div class="card stat">
        <div class="iconbox"><x-icon name="alert" size="22" /></div>
        <div class="label">Low Stock Items</div>
        <div class="value">{{ $lowstock }}</div>
        <div class="trend">Let the owner know</div>
    </div>
</div>

<div class="card" style="margin-top:18px">
    <div class="card-body" style="text-align:center;padding:36px">
        <h2>Ready to serve customers?</h2>
        <p class="muted">Open the point of sale to start ringing up a new order.</p>
        <a class="btn primary" href="{{ route('pos.index') }}"><x-icon name="cart" size="16" /> Open Point of Sale</a>
    </div>
</div>

<div class="card" style="margin-top:18px">
    <div class="card-head"><h3>My Recent Sales</h3><a class="btn ghost sm" href="{{ route('sales.index') }}">View all</a></div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Reference</th><th>Customer</th><th>Method</th><th class="num">Total</th><th>When</th><th></th></tr></thead>
            <tbody>
            @forelse($myrecent as $s)
                <tr>
                    <td><strong>{{ $s->reference }}</strong></td>
                    <td>{{ $s->customer ?? 'Walk-in' }}</td>
                    <td><span class="badge gray">{{ ucfirst($s->method) }}</span></td>
                    <td class="num">${{ number_format($s->total, 2) }}</td>
                    <td class="muted">{{ $s->createdat->diffForHumans() }}</td>
                    <td><a class="btn ghost sm" href="{{ route('sales.show', $s) }}">View</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">You haven't made any sales yet today.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
