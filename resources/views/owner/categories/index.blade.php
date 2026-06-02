@extends('layouts.app')
@section('title', 'Categories')

@section('content')
<div class="card">
    <div class="card-head" style="flex-wrap:wrap;gap:10px">
        <form method="GET" data-autosearch style="display:flex;gap:8px">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search categories…" style="padding:9px 12px;border:1px solid var(--line);border-radius:9px;min-width:200px">
            <noscript><button class="btn ghost sm">Search</button></noscript>
        </form>
        <button class="btn primary sm" data-modal-open="category-modal" data-create data-action="{{ route('owner.categories.store') }}"><x-icon name="plus" size="16" /> Add Category</button>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Name</th><th>Description</th><th class="num">Products</th><th></th></tr></thead>
            <tbody>
            @forelse($categories as $c)
                <tr>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td class="muted">{{ $c->description ?? '—' }}</td>
                    <td class="num">{{ $c->products_count }}</td>
                    <td>
                        <div class="btn-row">
                            <button class="btn ghost sm" data-modal-open="category-modal" data-action="{{ route('owner.categories.update', $c) }}" data-record='@json(['name' => $c->name, 'description' => $c->description])'>Edit</button>
                            <form method="POST" action="{{ route('owner.categories.destroy', $c) }}" class="ajax-form" onsubmit="return confirm('Archive this category?')">
                                @csrf @method('DELETE')
                                <button class="btn danger sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">No categories yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 20px 16px">{{ $categories->links() }}</div>
</div>

<div class="modal" id="category-modal">
    <div class="modal-card" style="max-width:520px">
        <div class="modal-head"><h3>Category</h3><button class="modal-close" data-modal-close type="button">&times;</button></div>
        <form class="ajax-form" method="POST" action="{{ route('owner.categories.store') }}">
            @csrf
            <div class="modal-body">
                <div class="field"><label>Name *</label><input type="text" name="name" required></div>
                <div class="field"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            </div>
            <div class="modal-foot">
                <button class="btn ghost" data-modal-close type="button">Cancel</button>
                <button class="btn primary" type="submit">Save Category</button>
            </div>
        </form>
    </div>
</div>
@endsection
