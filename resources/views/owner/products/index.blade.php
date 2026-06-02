@extends('layouts.app')
@section('title', 'Products')

@section('content')
<div class="card">
    <div class="card-head">
        <form method="GET" style="display:flex;gap:8px">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search products…" style="padding:8px 12px;border:1px solid var(--line);border-radius:9px">
            <button class="btn ghost sm">Search</button>
        </form>
        <a class="btn primary sm" href="{{ route('owner.products.create') }}">+ Add Product</a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Name</th><th>Category</th><th>Barcode</th><th class="num">Price</th><th class="num">Cost</th><th class="num">Stock</th><th>Expiry</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td>{{ $p->category->name ?? '—' }}</td>
                    <td class="muted">{{ $p->barcode ?? '—' }}</td>
                    <td class="num">${{ number_format($p->price, 2) }}</td>
                    <td class="num">${{ number_format($p->cost, 2) }}</td>
                    <td class="num">{{ $p->quantity }}</td>
                    <td>{{ $p->expiry?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @if($p->quantity == 0)<span class="badge red">Out of stock</span>
                        @elseif($p->isLowStock())<span class="badge amber">Low</span>
                        @else<span class="badge green">In stock</span>@endif
                    </td>
                    <td>
                        <div class="btn-row">
                            <a class="btn ghost sm" href="{{ route('owner.products.edit', $p) }}">Edit</a>
                            <form method="POST" action="{{ route('owner.products.destroy', $p) }}" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button class="btn danger sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="muted">No products found. Add your first product.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 20px 16px">{{ $products->links() }}</div>
</div>
@endsection
