<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Primary SEO --}}
    <title>PharmacyPOS — Smart Point of Sale & Inventory System for Pharmacies</title>
    <meta name="description" content="PharmacyPOS is a complete pharmacy point-of-sale and inventory management system. Track stock, manage staff, process sales fast, and grow your pharmacy with real-time reports.">
    <meta name="keywords" content="pharmacy pos, pharmacy point of sale, pharmacy inventory software, drug store management, medical store billing, pharmacy management system">
    <meta name="author" content="PharmacyPOS">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="PharmacyPOS — Smart Point of Sale for Pharmacies">
    <meta property="og:description" content="A complete pharmacy POS & inventory management system. Fast sales, smart stock control, and real-time reporting.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="PharmacyPOS">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="PharmacyPOS — Smart Point of Sale for Pharmacies">
    <meta name="twitter:description" content="A complete pharmacy POS & inventory management system.">

    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💊</text></svg>">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Structured data for rich search results --}}
    @verbatim
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "PharmacyPOS",
      "applicationCategory": "BusinessApplication",
      "operatingSystem": "Web",
      "description": "A complete pharmacy point-of-sale and inventory management system.",
      "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" }
    }
    </script>
    @endverbatim
    <style>
        .lp-nav { display:flex; align-items:center; justify-content:space-between; padding:18px 8%; }
        .lp-nav .logo-text { font-weight:800; font-size:1.2rem; display:flex; gap:10px; align-items:center; }
        .hero { padding:80px 8% 90px; text-align:center; background:linear-gradient(160deg,#ecfeff,#f1f5f9); }
        .hero h1 { font-size:3rem; max-width:820px; margin:0 auto .4em; }
        .hero p { font-size:1.2rem; color:var(--muted); max-width:620px; margin:0 auto 30px; }
        .features { padding:70px 8%; }
        .features h2 { text-align:center; font-size:2rem; margin-bottom:40px; }
        .feature { padding:26px; }
        .feature .fi { font-size:2rem; margin-bottom:12px; }
        .lp-foot { padding:34px 8%; border-top:1px solid var(--line); color:var(--muted); text-align:center; }
        .cta-band { background:var(--brand-dark); color:#fff; text-align:center; padding:60px 8%; }
        .cta-band h2 { color:#fff; font-size:2rem; }
    </style>
</head>
<body style="background:#fff">
    <header class="lp-nav">
        <div class="logo-text"><span class="logo" style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--brand),var(--brand-dark));display:grid;place-items:center;color:#fff">P</span> PharmacyPOS</div>
        <a class="btn primary" href="{{ route('login') }}">Sign in</a>
    </header>

    <section class="hero">
        <span class="badge teal">Pharmacy Point of Sale &amp; Inventory</span>
        <h1>Run your pharmacy with confidence.</h1>
        <p>PharmacyPOS gives owners full control over inventory, staff and revenue — while attendants ring up sales in seconds on a fast, modern checkout.</p>
        <div class="btn-row" style="justify-content:center">
            <a class="btn primary" href="{{ route('login') }}">Get started →</a>
            <a class="btn ghost" href="#features">See features</a>
        </div>
    </section>

    <section class="features" id="features">
        <h2>Everything a modern pharmacy needs</h2>
        <div class="grid cols-3">
            <div class="card feature"><div class="fi">🛒</div><h3>Fast Point of Sale</h3><p class="muted">Search, scan and check out in seconds with automatic stock deduction and instant receipts.</p></div>
            <div class="card feature"><div class="fi">📦</div><h3>Smart Inventory</h3><p class="muted">Track quantities, reorder levels and expiry dates with low-stock and expiry alerts.</p></div>
            <div class="card feature"><div class="fi">👥</div><h3>Owner &amp; Attendant Roles</h3><p class="muted">Separate, secure workspaces so staff only see what they need.</p></div>
            <div class="card feature"><div class="fi">📈</div><h3>Real-time Reports</h3><p class="muted">Revenue, best sellers and daily trends to help you make better decisions.</p></div>
            <div class="card feature"><div class="fi">🔒</div><h3>Secure by Design</h3><p class="muted">Hashed passwords, CSRF protection, rate-limiting and role-based access.</p></div>
            <div class="card feature"><div class="fi">🧾</div><h3>Printable Receipts</h3><p class="muted">Professional receipts ready to print or share for every transaction.</p></div>
        </div>
    </section>

    <section class="cta-band">
        <h2>Ready to modernise your pharmacy?</h2>
        <p>Sign in and explore the full owner and attendant experience.</p>
        <a class="btn" href="{{ route('login') }}" style="background:#fff;color:var(--brand-dark)">Sign in now</a>
    </section>

    <footer class="lp-foot">
        © {{ date('Y') }} PharmacyPOS. Built with Laravel. All rights reserved.
    </footer>
</body>
</html>
