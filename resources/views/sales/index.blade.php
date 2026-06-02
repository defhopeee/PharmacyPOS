@extends('layouts.app')
@section('title', 'Sales')

@section('content')
<div class="card">
    <div class="card-head">
        <form method="GET" style="display:flex;gap:8px">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by reference…" style="padding:8px 12px;border:1px solid var(--line);border-radius:9px">
            <button class="btn ghost sm">Search</button>
        </form>
        <a class="btn primary sm" href="{{ route('pos.index') }}">+ New Sale</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Reference</th><th>Attendant</th><th>Customer</th><th>Method</th><th class="num">Items</th><th class="num">Total</th><th>Date</th><th></th></tr></thead>
            <tbody>
            @forelse($sales as $s)
                <tr>
                    <td><strong>{{ $s->reference }}</strong></td>
                    <td>{{ $s->user->name ?? '—' }}</td>
                    <td>{{ $s->customer ?? 'Walk-in' }}</td>
                    <td><span class="badge gray">{{ ucfirst($s->method) }}</span></td>
                    <td class="num">{{ $s->items()->count() }}</td>
                    <td class="num">${{ number_format($s->total, 2) }}</td>
                    <td class="muted">{{ $s->createdat->format('d M Y, H:i') }}</td>
                    <td>
                        <div class="btn-row">
                            <a class="btn ghost sm" href="{{ route('sales.show', $s) }}">View</a>
                            <a class="btn ghost sm" href="{{ route('sales.receipt', $s) }}" target="_blank">Receipt</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted">No sales recorded.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 20px 16px">{{ $sales->links() }}</div>
</div>
@endsection
