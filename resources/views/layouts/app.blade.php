<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230f766e'><rect x='9' y='3' width='6' height='18' rx='1'/><rect x='3' y='9' width='18' height='6' rx='1'/></svg>">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php $user = auth()->user(); $owner = $user->isOwner(); @endphp
<div class="shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <span class="logo">P</span> {{ config('app.name') }}
        </div>

        <div class="role-pill">{{ $owner ? 'Owner Workspace' : 'Attendant Workspace' }}</div>
        <ul class="nav">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="ic"><x-icon name="grid" /></span> Dashboard</a></li>
            <li><a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}"><span class="ic"><x-icon name="cart" /></span> Point of Sale</a></li>
            <li><a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}"><span class="ic"><x-icon name="receipt" /></span> Sales</a></li>
            @if($owner)
                <div class="role-pill">Management</div>
                <li><a href="{{ route('owner.products.index') }}" class="{{ request()->routeIs('owner.products.*') ? 'active' : '' }}"><span class="ic"><x-icon name="package" /></span> Products</a></li>
                <li><a href="{{ route('owner.categories.index') }}" class="{{ request()->routeIs('owner.categories.*') ? 'active' : '' }}"><span class="ic"><x-icon name="folder" /></span> Categories</a></li>
                <li><a href="{{ route('owner.suppliers.index') }}" class="{{ request()->routeIs('owner.suppliers.*') ? 'active' : '' }}"><span class="ic"><x-icon name="truck" /></span> Suppliers</a></li>
                <li><a href="{{ route('owner.users.index') }}" class="{{ request()->routeIs('owner.users.*') ? 'active' : '' }}"><span class="ic"><x-icon name="users" /></span> Staff</a></li>
                <li><a href="{{ route('owner.reports.index') }}" class="{{ request()->routeIs('owner.reports.*') ? 'active' : '' }}"><span class="ic"><x-icon name="trending" /></span> Reports</a></li>
            @endif
        </ul>

        <div class="foot">
            <div class="who">{{ $user->name }}</div>
            <div class="muted">{{ $user->email }}</div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
                @csrf
                <button class="btn ghost sm block" style="color:#cbd5e1;border-color:rgba(255,255,255,.15)"><x-icon name="logout" size="16" /> Sign out</button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:14px">
                <button class="btn ghost sm no-print" onclick="document.getElementById('sidebar').classList.toggle('open')" style="display:none" id="menubtn"><x-icon name="menu" /></button>
                <span class="page-title">@yield('title', 'Dashboard')</span>
            </div>
            <div class="actions">
                <span class="badge teal">{{ ucfirst($user->role) }}</span>
                <a class="btn primary sm" href="{{ route('pos.index') }}"><x-icon name="plus" size="16" /> New Sale</a>
            </div>
        </header>

        <main class="content">
            @if(session('status'))
                <div class="alert ok">{{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="alert err">{{ session('error') }}</div>
            @endif
            @if($errors->any() && ! request()->routeIs('pos.*'))
                <div class="alert err">
                    <strong>Please fix the following:</strong>
                    <ul style="margin:6px 0 0 18px">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    if (window.innerWidth <= 980) { document.getElementById('menubtn').style.display = 'inline-flex'; }
</script>
@stack('scripts')
</body>
</html>
