@extends('layouts.app')
@section('title', $user->exists ? 'Edit Staff' : 'Add Staff')

@section('content')
<div class="card" style="max-width:620px">
    <div class="card-head"><h3>{{ $user->exists ? 'Edit Staff Member' : 'New Staff Member' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $user->exists ? route('owner.users.update', $user) : route('owner.users.store') }}">
            @csrf
            @if($user->exists) @method('PUT') @endif

            <div class="field">
                <label>Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="off">
                    @error('email')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Role *</label>
                    <select name="role" required>
                        <option value="attendant" @selected(old('role', $user->role) === 'attendant')>Attendant</option>
                        <option value="owner" @selected(old('role', $user->role) === 'owner')>Owner</option>
                    </select>
                </div>
                <div class="field">
                    <label>Status</label>
                    <select name="active">
                        <option value="1" @selected(old('active', $user->exists ? $user->active : true))>Active</option>
                        <option value="0" @selected(old('active', $user->exists ? $user->active : true) == false)>Disabled</option>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Password {{ $user->exists ? '(leave blank to keep)' : '*' }}</label>
                    <input type="password" name="password" autocomplete="new-password" {{ $user->exists ? '' : 'required' }}>
                    @error('password')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password">
                </div>
            </div>
            <p class="muted" style="font-size:.8rem;margin-top:-6px">Minimum 8 characters with upper &amp; lower case, a number and a symbol.</p>

            <div class="btn-row">
                <button class="btn primary">{{ $user->exists ? 'Update Staff' : 'Create Staff' }}</button>
                <a class="btn ghost" href="{{ route('owner.users.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
