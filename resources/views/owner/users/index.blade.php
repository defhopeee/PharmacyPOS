@extends('layouts.app')
@section('title', 'Staff')

@section('content')
<div class="card">
    <div class="card-head" style="flex-wrap:wrap;gap:10px">
        <form method="GET" data-autosearch style="display:flex;gap:8px">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search staff…" style="padding:9px 12px;border:1px solid var(--line);border-radius:9px;min-width:200px">
            <noscript><button class="btn ghost sm">Search</button></noscript>
        </form>
        <button class="btn primary sm" data-modal-open="user-modal" data-create data-action="{{ route('owner.users.store') }}"><x-icon name="plus" size="16" /> Add Staff</button>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($users as $u)
                @php $record = ['name' => $u->name, 'email' => $u->email, 'phone' => $u->phone, 'role' => $u->role, 'active' => (bool) $u->active]; @endphp
                <tr>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->phone ?? '—' }}</td>
                    <td><span class="badge {{ $u->role === 'owner' ? 'teal' : 'gray' }}">{{ ucfirst($u->role) }}</span></td>
                    <td>@if($u->active)<span class="badge green">Active</span>@else<span class="badge red">Disabled</span>@endif</td>
                    <td>
                        <div class="btn-row">
                            <button class="btn ghost sm" data-modal-open="user-modal" data-action="{{ route('owner.users.update', $u) }}" data-record='@json($record)'>Edit</button>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('owner.users.destroy', $u) }}" class="ajax-form" onsubmit="return confirm('Archive {{ addslashes($u->name) }}? Their sales history is kept.')">
                                @csrf @method('DELETE')
                                <button class="btn danger sm">Archive</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No staff yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 20px 16px">{{ $users->links() }}</div>
</div>

@if($archived->count())
<div class="card" style="margin-top:18px">
    <div class="card-head"><h3>Archived Staff</h3><span class="badge gray">{{ $archived->count() }}</span></div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Archived</th><th></th></tr></thead>
            <tbody>
            @foreach($archived as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td class="muted">{{ $u->email }}</td>
                    <td><span class="badge gray">{{ ucfirst($u->role) }}</span></td>
                    <td class="muted">{{ $u->deletedat?->diffForHumans() }}</td>
                    <td>
                        <form method="POST" action="{{ route('owner.users.restore', $u->id) }}" class="ajax-form">
                            @csrf
                            <button class="btn ghost sm"><x-icon name="restore" size="15" /> Restore</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <p class="muted" style="padding:0 20px 16px;font-size:.82rem">Archived staff keep their sales history. Restore re-enables their login.</p>
</div>
@endif

<div class="modal" id="user-modal">
    <div class="modal-card">
        <div class="modal-head"><h3>Staff Member</h3><button class="modal-close" data-modal-close type="button">&times;</button></div>
        <form class="ajax-form" method="POST" action="{{ route('owner.users.store') }}">
            @csrf
            <div class="modal-body">
                <div class="field"><label>Full Name *</label><input type="text" name="name" required></div>
                <div class="form-grid">
                    <div class="field"><label>Email *</label><input type="email" name="email" required autocomplete="off"></div>
                    <div class="field"><label>Phone</label><input type="text" name="phone"></div>
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Role *</label>
                        <select name="role" required>
                            <option value="attendant">Attendant</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Status</label><br>
                        <label class="switch"><input type="checkbox" name="active" value="1" checked><span class="track"></span> <span>Active</span></label>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field"><label>Password</label><input type="password" name="password" autocomplete="new-password" placeholder="Required for new staff"></div>
                    <div class="field"><label>Confirm Password</label><input type="password" name="password_confirmation" autocomplete="new-password"></div>
                </div>
                <p class="muted" style="font-size:.8rem;margin:0">Min 8 chars with upper &amp; lower case, a number and a symbol. Leave blank when editing to keep the current password.</p>
            </div>
            <div class="modal-foot">
                <button class="btn ghost" data-modal-close type="button">Cancel</button>
                <button class="btn primary" type="submit">Save Staff</button>
            </div>
        </form>
    </div>
</div>
@endsection
