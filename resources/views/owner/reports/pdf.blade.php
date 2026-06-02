<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { color: #1e293b; font-size: 11px; }
    h1 { font-size: 18px; margin: 0 0 2px; color: #0f766e; }
    h2 { font-size: 13px; margin: 18px 0 6px; color: #0f766e; border-bottom: 1px solid #cbd5e1; padding-bottom: 3px; }
    .muted { color: #64748b; }
    .head { border-bottom: 2px solid #0f766e; padding-bottom: 8px; margin-bottom: 6px; }
    .summary td { padding: 8px 10px; background: #f1f5f9; border-radius: 6px; }
    .summary .lbl { font-size: 9px; text-transform: uppercase; color: #64748b; }
    .summary .val { font-size: 15px; font-weight: bold; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th { text-align: left; font-size: 9px; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #cbd5e1; padding: 5px 6px; }
    table.data td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; }
    .num { text-align: right; }
    .foot { margin-top: 16px; font-size: 9px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
    <div class="head">
        <h1>PharmacyPOS — Sales Report</h1>
        <div class="muted">
            Period: {{ \Illuminate\Support\Carbon::parse($from)->format('d M Y') }} to {{ \Illuminate\Support\Carbon::parse($to)->format('d M Y') }}
            &nbsp;•&nbsp; Generated {{ $generatedAt->format('d M Y H:i') }}
        </div>
    </div>

    <table class="summary" width="100%" cellspacing="6">
        <tr>
            <td width="33%"><div class="lbl">Total Sales</div><div class="val">{{ money($totalsales) }}</div></td>
            <td width="33%"><div class="lbl">Orders</div><div class="val">{{ $ordercount }}</div></td>
            <td width="33%"><div class="lbl">Average Order</div><div class="val">{{ money($average) }}</div></td>
        </tr>
    </table>

    <h2>Sales by Staff (accountability)</h2>
    <table class="data">
        <thead><tr><th>Staff</th><th class="num">Orders</th><th class="num">Total</th></tr></thead>
        <tbody>
        @forelse($bystaff as $r)
            <tr><td>{{ $r->user->name ?? '—' }}</td><td class="num">{{ $r->orders }}</td><td class="num">{{ money($r->total) }}</td></tr>
        @empty
            <tr><td colspan="3" class="muted">No data.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Sales by Payment Method</h2>
    <table class="data">
        <thead><tr><th>Method</th><th class="num">Orders</th><th class="num">Total</th></tr></thead>
        <tbody>
        @forelse($bymethod as $m)
            <tr><td>{{ strtoupper($m->method) }}</td><td class="num">{{ $m->orders }}</td><td class="num">{{ money($m->total) }}</td></tr>
        @empty
            <tr><td colspan="3" class="muted">No data.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Best Selling Products</h2>
    <table class="data">
        <thead><tr><th>Product</th><th class="num">Qty Sold</th><th class="num">Revenue</th></tr></thead>
        <tbody>
        @forelse($topproducts as $tp)
            <tr><td>{{ $tp->name }}</td><td class="num">{{ $tp->sold }}</td><td class="num">{{ money($tp->earned) }}</td></tr>
        @empty
            <tr><td colspan="3" class="muted">No data.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Transaction Log — who sold what, when</h2>
    <table class="data">
        <thead><tr><th>Date &amp; Time</th><th>Reference</th><th>Sold by</th><th>Items</th><th class="num">Total</th><th>Method</th></tr></thead>
        <tbody>
        @forelse($log as $s)
            <tr>
                <td>{{ $s->createdat->format('d M Y H:i') }}</td>
                <td>{{ $s->reference }}</td>
                <td>{{ $s->user->name ?? '—' }}</td>
                <td>{{ $s->items->map(fn($i) => $i->name.' x'.$i->quantity)->implode(', ') }}</td>
                <td class="num">{{ money($s->total) }}</td>
                <td>{{ strtoupper($s->method) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">No transactions in this period.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="foot">PharmacyPOS · Confidential business report</div>
</body>
</html>
