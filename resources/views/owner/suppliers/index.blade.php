@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="card">
    <div class="card-head" style="flex-wrap:wrap;gap:10px">
        <form method="GET" data-autosearch style="display:flex;gap:8px">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search suppliers…" style="padding:9px 12px;border:1px solid var(--line);border-radius:9px;min-width:200px">
            <noscript><button class="btn ghost sm">Search</button></noscript>
        </form>
        <button class="btn primary sm" data-modal-open="supplier-modal" data-create data-action="{{ route('owner.suppliers.store') }}"><x-icon name="plus" size="16" /> Add Supplier</button>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Address</th><th class="num">Products</th><th></th></tr></thead>
            <tbody>
            @forelse($suppliers as $s)
                <tr>
                    <td><strong>{{ $s->name }}</strong></td>
                    <td>{{ $s->phone ?? '—' }}</td>
                    <td>{{ $s->email ?? '—' }}</td>
                    <td class="muted">{{ $s->address ?? '—' }}</td>
                    <td class="num">{{ $s->products_count }}</td>
                    <td>
                        <div class="btn-row">
                            <button class="btn ghost sm" data-modal-open="supplier-modal" data-action="{{ route('owner.suppliers.update', $s) }}" data-record='@json(['name' => $s->name, 'phone' => $s->phone, 'email' => $s->email, 'address' => $s->address])'>Edit</button>
                            <form method="POST" action="{{ route('owner.suppliers.destroy', $s) }}" class="ajax-form" onsubmit="return confirm('Archive this supplier?')">
                                @csrf @method('DELETE')
                                <button class="btn danger sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No suppliers yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 20px 16px">{{ $suppliers->links() }}</div>
</div>

<div class="modal" id="supplier-modal">
    <div class="modal-card" style="max-width:580px">
        <div class="modal-head"><h3>Supplier</h3><button class="modal-close" data-modal-close type="button">&times;</button></div>
        <form class="ajax-form" method="POST" action="{{ route('owner.suppliers.store') }}">
            @csrf
            <div class="modal-body">
                <div class="field"><label>Name *</label><input type="text" name="name" required></div>
                <div class="form-grid">
                    <div class="field"><label>Phone</label><input type="text" name="phone"></div>
                    <div class="field"><label>Email</label><input type="email" name="email"></div>
                </div>
                <div class="field"><label>Address</label><textarea name="address" rows="2"></textarea></div>
            </div>
            <div class="modal-foot">
                <button class="btn ghost" data-modal-close type="button">Cancel</button>
                <button class="btn primary" type="submit">Save Supplier</button>
            </div>
        </form>
    </div>
</div>
@endsection
