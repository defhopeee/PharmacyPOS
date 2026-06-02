@extends('layouts.app')
@section('title', $category->exists ? 'Edit Category' : 'Add Category')

@section('content')
<div class="card" style="max-width:560px">
    <div class="card-head"><h3>{{ $category->exists ? 'Edit Category' : 'New Category' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $category->exists ? route('owner.categories.update', $category) : route('owner.categories.store') }}">
            @csrf
            @if($category->exists) @method('PUT') @endif
            <div class="field">
                <label>Name *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required>
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Description</label>
                <textarea name="description" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="btn-row">
                <button class="btn primary">{{ $category->exists ? 'Update' : 'Create' }}</button>
                <a class="btn ghost" href="{{ route('owner.categories.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
