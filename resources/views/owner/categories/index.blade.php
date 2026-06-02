@extends('layouts.app')
@section('title', 'Categories')

@section('content')
<div class="card">
    <div class="card-head"><h3>Product Categories</h3><a class="btn primary sm" href="{{ route('owner.categories.create') }}">+ Add Category</a></div>
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
                            <a class="btn ghost sm" href="{{ route('owner.categories.edit', $c) }}">Edit</a>
                            <form method="POST" action="{{ route('owner.categories.destroy', $c) }}" onsubmit="return confirm('Delete this category?')">
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
@endsection
