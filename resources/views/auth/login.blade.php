<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in · {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="stylesheet" href="{{ asset_v('css/app.css') }}">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-side">
        <div class="auth-top">
            <div class="auth-brand">
                <img src="{{ asset('images/logo.svg') }}" alt="PharmacyPOS">
                <span>PharmacyPOS</span>
            </div>
            <h1>Run your pharmacy<br>with ease.</h1>
            <p>The complete point-of-sale and inventory platform built for modern Kenyan pharmacies.</p>
        </div>

        <ul class="auth-features">
            <li><span class="afi"><x-icon name="cart" size="18" /></span> Lightning-fast checkout for attendants</li>
            <li><span class="afi"><x-icon name="package" size="18" /></span> Full inventory &amp; staff control for owners</li>
            <li><span class="afi"><x-icon name="trending" size="18" /></span> Real-time sales reporting &amp; alerts</li>
            <li><span class="afi"><x-icon name="lock" size="18" /></span> Secure, role-based access</li>
        </ul>

        <div class="auth-foot">
            <span class="chip"><x-icon name="dollar" size="14" /> KSh pricing</span>
            <span class="chip"><x-icon name="phone" size="14" /> M-Pesa ready</span>
            <span class="chip"><x-icon name="check" size="14" /> Soft deletes &amp; backups</span>
        </div>
    </div>

    <div class="auth-form">
        <div class="box">
            <h2 style="font-size:1.5rem">Welcome back</h2>
            <p class="muted" style="margin-top:0">Sign in to your account to continue.</p>

            @if($errors->any())
                <div class="alert err">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="field">
                    <label for="phone">Phone number</label>
                    <div class="input-icon">
                        <span class="ii"><x-icon name="phone" size="17" /></span>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required autofocus autocomplete="username" placeholder="0712345678">
                    </div>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <span class="ii"><x-icon name="lock" size="17" /></span>
                        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    </div>
                </div>
                <div class="field" style="display:flex;align-items:center;gap:8px">
                    <input type="checkbox" id="remember" name="remember" style="width:auto">
                    <label for="remember" style="margin:0">Remember me on this device</label>
                </div>
                <button type="submit" class="btn primary block"><x-icon name="logout" size="16" /> Sign in</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
