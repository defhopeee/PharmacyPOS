@extends('layouts.app')
@section('title', 'Staff')

@section('content')
<div class="card">
    <div class="card-head"><h3>Staff Accounts</h3><a class="btn primary sm" href="{{ route('owner.users.create') }}">+ Add Staff</a></div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->phone ?? '—' }}</td>
                    <td><span class="badge {{ $u->role === 'owner' ? 'teal' : 'gray' }}">{{ ucfirst($u->role) }}</span></td>
                    <td>@if($u->active)<span class="badge green">Active</span>@else<span class="badge red">Disabled</span>@endif</td>
                    <td>
                        <div class="btn-row">
                            <a class="btn ghost sm" href="{{ route('owner.users.edit', $u) }}">Edit</a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('owner.users.destroy', $u) }}" onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button class="btn danger sm">Delete</button>
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
@endsection
