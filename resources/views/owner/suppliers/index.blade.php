@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="card">
    <div class="card-head"><h3>Suppliers</h3><a class="btn primary sm" href="{{ route('owner.suppliers.create') }}">+ Add Supplier</a></div>
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
                            <a class="btn ghost sm" href="{{ route('owner.suppliers.edit', $s) }}">Edit</a>
                            <form method="POST" action="{{ route('owner.suppliers.destroy', $s) }}" onsubmit="return confirm('Delete this supplier?')">
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
@endsection
