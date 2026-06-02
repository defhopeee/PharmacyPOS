@extends('layouts.app')
@section('title', $supplier->exists ? 'Edit Supplier' : 'Add Supplier')

@section('content')
<div class="card" style="max-width:620px">
    <div class="card-head"><h3>{{ $supplier->exists ? 'Edit Supplier' : 'New Supplier' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $supplier->exists ? route('owner.suppliers.update', $supplier) : route('owner.suppliers.store') }}">
            @csrf
            @if($supplier->exists) @method('PUT') @endif
            <div class="field">
                <label>Name *</label>
                <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required>
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="form-grid">
                <div class="field">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}">
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}">
                    @error('email')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="field">
                <label>Address</label>
                <textarea name="address" rows="2">{{ old('address', $supplier->address) }}</textarea>
            </div>
            <div class="btn-row">
                <button class="btn primary">{{ $supplier->exists ? 'Update' : 'Create' }}</button>
                <a class="btn ghost" href="{{ route('owner.suppliers.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
