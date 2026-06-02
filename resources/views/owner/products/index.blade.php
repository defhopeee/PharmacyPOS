@extends('layouts.app')
@section('title', 'Products')

@section('content')
<div class="card">
    <div class="card-head" style="flex-wrap:wrap;gap:10px">
        <form method="GET" data-autosearch style="display:flex;gap:8px;flex-wrap:wrap">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search name or barcode…" style="padding:9px 12px;border:1px solid var(--line);border-radius:9px;min-width:200px">
            <select name="categoryid" style="padding:9px 12px;border:1px solid var(--line);border-radius:9px">
                <option value="">All categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected($categoryid == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <noscript><button class="btn ghost sm">Filter</button></noscript>
        </form>
        <button class="btn primary sm" data-modal-open="product-modal" data-create data-action="{{ route('owner.products.store') }}"><x-icon name="plus" size="16" /> Add Product</button>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Name</th><th>Category</th><th>Barcode</th><th class="num">Price</th><th class="num">Cost</th><th class="num">Stock</th><th>Expiry</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($products as $p)
                @php
                    $record = [
                        'name' => $p->name, 'categoryid' => $p->categoryid, 'supplierid' => $p->supplierid,
                        'barcode' => $p->barcode, 'description' => $p->description,
                        'price' => $p->price, 'cost' => $p->cost, 'quantity' => $p->quantity,
                        'reorder' => $p->reorder, 'expiry' => $p->expiry?->format('Y-m-d'),
                    ];
                @endphp
                <tr>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td>{{ $p->category->name ?? '—' }}</td>
                    <td class="muted">{{ $p->barcode ?? '—' }}</td>
                    <td class="num">{{ money($p->price) }}</td>
                    <td class="num">{{ money($p->cost) }}</td>
                    <td class="num">{{ $p->quantity }}</td>
                    <td>{{ $p->expiry?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @if($p->quantity == 0)<span class="badge red">Out of stock</span>
                        @elseif($p->isLowStock())<span class="badge amber">Low ({{ $p->quantity }})</span>
                        @else<span class="badge green">In stock</span>@endif
                    </td>
                    <td>
                        <div class="btn-row">
                            <button class="btn ghost sm" data-modal-open="product-modal" data-action="{{ route('owner.products.update', $p) }}" data-record='@json($record)'>Edit</button>
                            <form method="POST" action="{{ route('owner.products.destroy', $p) }}" class="ajax-form" onsubmit="return confirm('Archive {{ addslashes($p->name) }}?')">
                                @csrf @method('DELETE')
                                <button class="btn danger sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="muted">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 20px 16px">{{ $products->links() }}</div>
</div>

{{-- Create / Edit modal --}}
<div class="modal" id="product-modal">
    <div class="modal-card">
        <div class="modal-head">
            <h3>Product</h3>
            <button class="modal-close" data-modal-close type="button">&times;</button>
        </div>
        <form class="ajax-form" method="POST" action="{{ route('owner.products.store') }}">
            @csrf
            <div class="modal-body">
                <div class="field">
                    <label>Product Name *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Category</label>
                        <select name="categoryid">
                            <option value="">— None —</option>
                            @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Supplier</label>
                        <select name="supplierid">
                            <option value="">— None —</option>
                            @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field"><label>Buying Price (KSh) *</label><input type="number" name="cost" step="0.01" min="0" value="0" required></div>
                    <div class="field"><label>Selling Price (KSh) *</label><input type="number" name="price" step="0.01" min="0" value="0" required></div>
                </div>
                <div class="form-grid">
                    <div class="field"><label>Quantity in Stock *</label><input type="number" name="quantity" min="0" value="0" required></div>
                    <div class="field"><label>Alert When Stock Below *</label><input type="number" name="reorder" min="0" value="10" required></div>
                </div>
                <div class="form-grid">
                    <div class="field"><label>Barcode</label><input type="text" name="barcode"></div>
                    <div class="field"><label>Expiry Date</label><input type="date" name="expiry"></div>
                </div>
                <div class="field"><label>Description</label><textarea name="description" rows="2"></textarea></div>
            </div>
            <div class="modal-foot">
                <button class="btn ghost" data-modal-close type="button">Cancel</button>
                <button class="btn primary" type="submit">Save Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
