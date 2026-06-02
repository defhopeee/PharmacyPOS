<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in · {{ config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💊</text></svg>">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-side">
        <div class="logo" style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,var(--brand),#fff2);display:grid;place-items:center;font-weight:900;font-size:1.4rem;margin-bottom:24px">P</div>
        <h1>PharmacyPOS</h1>
        <p>The complete point-of-sale and inventory platform built for modern pharmacies.</p>
        <ul>
            <li>✓ Lightning-fast checkout for attendants</li>
            <li>✓ Full inventory &amp; staff control for owners</li>
            <li>✓ Real-time sales reporting</li>
            <li>✓ Secure, role-based access</li>
        </ul>
    </div>

    <div class="auth-form">
        <div class="box">
            <h2 style="font-size:1.6rem">Welcome back</h2>
            <p class="muted" style="margin-top:0">Sign in to your account to continue.</p>

            @if($errors->any())
                <div class="alert err">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="field">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@pharmacy.test">
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                </div>
                <div class="field" style="display:flex;align-items:center;gap:8px">
                    <input type="checkbox" id="remember" name="remember" style="width:auto">
                    <label for="remember" style="margin:0">Remember me on this device</label>
                </div>
                <button type="submit" class="btn primary block">Sign in</button>
            </form>

            <div class="cred-hint">
                <strong>Demo accounts</strong> (password: <code>Password123!</code>)<br>
                Owner — <code>owner@pharmacypos.test</code><br>
                Attendant — <code>attendant@pharmacypos.test</code>
            </div>
        </div>
    </div>
</div>
</body>
</html>
