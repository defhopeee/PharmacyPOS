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
            <a class="btn ghost" href="{{ route('owner.reports.pdf', request()->only('from', 'to')) }}"><x-icon name="download" size="16" /> Download PDF</a>
            <a class="btn ghost" href="{{ route('owner.backup') }}"><x-icon name="download" size="16" /> Backup Database</a>
        </form>
    </div>
</div>

<div class="grid cols-3">
    <div class="card stat"><div class="iconbox"><x-icon name="dollar" size="22" /></div><div class="label">Total Sales</div><div class="value">{{ money($totalsales) }}</div></div>
    <div class="card stat"><div class="iconbox"><x-icon name="receipt" size="22" /></div><div class="label">Orders</div><div class="value">{{ $ordercount }}</div></div>
    <div class="card stat"><div class="iconbox"><x-icon name="chart" size="22" /></div><div class="label">Average Order</div><div class="value">{{ money($average) }}</div></div>
</div>

<div class="grid cols-2" style="margin-top:18px">
    <div class="card">
        <div class="card-head"><h3>Sales by Payment Method</h3></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Method</th><th class="num">Orders</th><th class="num">Total</th></tr></thead>
                <tbody>
                @forelse($bymethod as $m)
                    <tr><td>{{ strtoupper($m->method) }}</td><td class="num">{{ $m->orders }}</td><td class="num">{{ money($m->total) }}</td></tr>
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
                    <tr><td>{{ $tp->name }}</td><td class="num">{{ $tp->sold }}</td><td class="num">{{ money($tp->earned) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="muted">No data in this range.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid cols-2" style="margin-top:18px">
    <div class="card">
        <div class="card-head"><h3>Sales by Staff</h3></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Staff</th><th class="num">Orders</th><th class="num">Total</th></tr></thead>
                <tbody>
                @forelse($bystaff as $r)
                    <tr><td>{{ $r->user->name ?? '—' }}</td><td class="num">{{ $r->orders }}</td><td class="num">{{ money($r->total) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="muted">No data in this range.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-head"><h3>Daily Breakdown</h3></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Date</th><th class="num">Orders</th><th class="num">Total</th></tr></thead>
                <tbody>
                @forelse($byday as $d)
                    <tr><td>{{ \Illuminate\Support\Carbon::parse($d->day)->format('d M Y') }}</td><td class="num">{{ $d->orders }}</td><td class="num">{{ money($d->total) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="muted">No sales in this range.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card" style="margin-top:18px">
    <div class="card-head"><h3>Transaction Log: who sold what, when</h3><span class="muted" style="font-size:.8rem">Latest {{ $log->count() }}</span></div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Date &amp; Time</th><th>Reference</th><th>Sold by</th><th>Items</th><th class="num">Total</th><th>Method</th></tr></thead>
            <tbody>
            @forelse($log as $s)
                <tr>
                    <td class="muted">{{ $s->createdat->format('d M Y, H:i') }}</td>
                    <td><strong>{{ $s->reference }}</strong></td>
                    <td>{{ $s->user->name ?? '—' }}</td>
                    <td class="muted">{{ \Illuminate\Support\Str::limit($s->items->map(fn($i) => $i->name.' x'.$i->quantity)->implode(', '), 60) }}</td>
                    <td class="num">{{ money($s->total) }}</td>
                    <td><span class="badge gray">{{ strtoupper($s->method) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No transactions in this range.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
