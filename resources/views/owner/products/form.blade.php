@extends('layouts.app')
@section('title', $product->exists ? 'Edit Product' : 'Add Product')

@section('content')
<div class="card" style="max-width:760px">
    <div class="card-head"><h3>{{ $product->exists ? 'Edit Product' : 'New Product' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $product->exists ? route('owner.products.update', $product) : route('owner.products.store') }}">
            @csrf
            @if($product->exists) @method('PUT') @endif

            <div class="field">
                <label>Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required>
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Category</label>
                    <select name="categoryid">
                        <option value="">— None —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('categoryid', $product->categoryid) == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Supplier</label>
                    <select name="supplierid">
                        <option value="">— None —</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(old('supplierid', $product->supplierid) == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Selling Price *</label>
                    <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $product->price ?? 0) }}" required>
                    @error('price')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Cost Price *</label>
                    <input type="number" name="cost" step="0.01" min="0" value="{{ old('cost', $product->cost ?? 0) }}" required>
                    @error('cost')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Quantity in Stock *</label>
                    <input type="number" name="quantity" min="0" value="{{ old('quantity', $product->quantity ?? 0) }}" required>
                    @error('quantity')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Reorder Level *</label>
                    <input type="number" name="reorder" min="0" value="{{ old('reorder', $product->reorder ?? 10) }}" required>
                    @error('reorder')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                    @error('barcode')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry" value="{{ old('expiry', $product->expiry?->format('Y-m-d')) }}">
                    @error('expiry')<div class="err">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description" rows="3">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="btn-row">
                <button class="btn primary">{{ $product->exists ? 'Update Product' : 'Create Product' }}</button>
                <a class="btn ghost" href="{{ route('owner.products.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
