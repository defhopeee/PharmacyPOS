@extends('layouts.app')
@section('title', 'Owner Dashboard')

@section('content')
@php
    $maxchart = max($chart->max('total'), 1);
@endphp

<div class="grid cols-4">
    <div class="card stat">
        <div class="iconbox"><x-icon name="dollar" size="22" /></div>
        <div class="label">Sales Today</div>
        <div class="value">${{ number_format($salestoday, 2) }}</div>
        <div class="trend">{{ $orderstoday }} orders today</div>
    </div>
    <div class="card stat">
        <div class="iconbox"><x-icon name="calendar" size="22" /></div>
        <div class="label">This Month</div>
        <div class="value">${{ number_format($salesmonth, 2) }}</div>
        <div class="trend">Month to date</div>
    </div>
    <div class="card stat">
        <div class="iconbox"><x-icon name="briefcase" size="22" /></div>
        <div class="label">Total Revenue</div>
        <div class="value">${{ number_format($revenue, 2) }}</div>
        <div class="trend">All time</div>
    </div>
    <div class="card stat">
        <div class="iconbox"><x-icon name="package" size="22" /></div>
        <div class="label">Products / Staff</div>
        <div class="value">{{ $productcount }} <span class="muted" style="font-size:1rem">/ {{ $usercount }}</span></div>
        <div class="trend">In catalog</div>
    </div>
</div>

<div class="grid cols-3" style="margin-top:18px">
    <div class="card" style="grid-column: span 2">
        <div class="card-head"><h3>Sales — last 7 days</h3></div>
        <div class="card-body">
            <div style="display:flex;align-items:flex-end;gap:14px;height:200px">
                @foreach($chart as $c)
                    <div style="flex:1;text-align:center;display:flex;flex-direction:column;justify-content:flex-end;height:100%">
                        <div class="muted" style="font-size:.75rem;margin-bottom:4px">${{ number_format($c['total'], 0) }}</div>
                        <div title="${{ number_format($c['total'],2) }}" style="background:linear-gradient(180deg,var(--brand),var(--brand-dark));border-radius:8px 8px 0 0;height:{{ max(4, ($c['total'] / $maxchart) * 160) }}px"></div>
                        <div class="muted" style="font-size:.78rem;margin-top:6px">{{ $c['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h3>Top Products</h3></div>
        <div class="card-body">
            @forelse($topproducts as $tp)
                <div class="flex-between" style="padding:8px 0;border-bottom:1px solid var(--line)">
                    <div>{{ $tp->name }}</div>
                    <span class="badge teal">{{ $tp->sold }} sold</span>
                </div>
            @empty
                <p class="muted">No sales recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="grid cols-2" style="margin-top:18px">
    <div class="card">
        <div class="card-head"><h3>Low Stock Alerts</h3><span class="badge red">{{ $lowstock->count() }}</span></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Product</th><th class="num">In stock</th><th class="num">Reorder at</th></tr></thead>
                <tbody>
                @forelse($lowstock->take(6) as $p)
                    <tr><td>{{ $p->name }}</td><td class="num"><span class="badge {{ $p->quantity == 0 ? 'red' : 'amber' }}">{{ $p->quantity }}</span></td><td class="num">{{ $p->reorder }}</td></tr>
                @empty
                    <tr><td colspan="3" class="muted">All products are well stocked.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h3>Expiring Soon (30 days)</h3><span class="badge amber">{{ $expiring->count() }}</span></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Product</th><th>Expiry</th><th class="num">Qty</th></tr></thead>
                <tbody>
                @forelse($expiring->take(6) as $p)
                    <tr><td>{{ $p->name }}</td><td>{{ $p->expiry?->format('d M Y') }}</td><td class="num">{{ $p->quantity }}</td></tr>
                @empty
                    <tr><td colspan="3" class="muted">Nothing expiring soon.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card" style="margin-top:18px">
    <div class="card-head"><h3>Recent Sales</h3><a class="btn ghost sm" href="{{ route('sales.index') }}">View all</a></div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Reference</th><th>Attendant</th><th>Customer</th><th>Method</th><th class="num">Total</th><th>When</th><th></th></tr></thead>
            <tbody>
            @forelse($recentsales as $s)
                <tr>
                    <td><strong>{{ $s->reference }}</strong></td>
                    <td>{{ $s->user->name ?? '—' }}</td>
                    <td>{{ $s->customer ?? 'Walk-in' }}</td>
                    <td><span class="badge gray">{{ ucfirst($s->method) }}</span></td>
                    <td class="num">${{ number_format($s->total, 2) }}</td>
                    <td class="muted">{{ $s->createdat->diffForHumans() }}</td>
                    <td><a class="btn ghost sm" href="{{ route('sales.show', $s) }}">View</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No sales yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
