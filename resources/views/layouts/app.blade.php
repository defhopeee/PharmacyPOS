<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="stylesheet" href="{{ asset_v('css/app.css') }}">
</head>
<body>
@php $user = auth()->user(); $owner = $user->isOwner(); @endphp
<div class="shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo.svg') }}" alt="PharmacyPOS" style="width:34px;height:34px;border-radius:9px"> {{ config('app.name') }}
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
                <button class="btn ghost sm no-print" id="menubtn" aria-label="Menu"><x-icon name="menu" /></button>
                <span class="page-title">@yield('title', 'Dashboard')</span>
            </div>
            <div class="actions">
                <div class="bell no-print">
                    <button class="bell-btn" id="bellbtn" type="button" aria-label="Notifications">
                        <x-icon name="alert" size="18" />
                        @if($notifcount > 0)<span class="bell-count">{{ $notifcount > 99 ? '99+' : $notifcount }}</span>@endif
                    </button>
                    <div class="bell-menu" id="bellmenu">
                        <div class="bm-head">Notifications ({{ $notifcount }})</div>
                        @forelse($notifications as $n)
                            <a class="bell-item {{ $n['type'] }}" href="{{ $n['url'] }}">
                                <span class="bi-ic"><x-icon name="{{ $n['icon'] }}" size="16" /></span>
                                <div>
                                    <div class="bi-t">{{ $n['title'] }}</div>
                                    <div class="bi-m">{{ $n['meta'] }}</div>
                                </div>
                            </a>
                        @empty
                            <div class="bell-empty">All good — no alerts right now.</div>
                        @endforelse
                    </div>
                </div>
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
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<script>
    // Mobile sidebar toggle
    (function () {
        const sb = document.getElementById('sidebar');
        const bd = document.getElementById('sidebarBackdrop');
        const btn = document.getElementById('menubtn');
        function toggle(open) {
            sb.classList.toggle('open', open);
            bd.classList.toggle('open', open);
        }
        if (btn) btn.addEventListener('click', () => toggle(!sb.classList.contains('open')));
        if (bd) bd.addEventListener('click', () => toggle(false));
        sb.querySelectorAll('.nav a').forEach(a => a.addEventListener('click', () => toggle(false)));
    })();
    // Notifications bell toggle
    (function () {
        const btn = document.getElementById('bellbtn');
        const menu = document.getElementById('bellmenu');
        if (btn) {
            btn.addEventListener('click', e => { e.stopPropagation(); menu.classList.toggle('open'); });
            document.addEventListener('click', e => { if (!menu.contains(e.target)) menu.classList.remove('open'); });
        }
    })();
</script>
<script src="{{ asset_v('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
