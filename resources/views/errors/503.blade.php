<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="60">
    <title>We'll be right back — 3G Computers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at 15% 20%, rgba(80, 160, 255, .35), transparent 40%),
                radial-gradient(circle at 85% 75%, rgba(80, 160, 255, .3), transparent 45%),
                linear-gradient(135deg, #061336 0%, #0b2559 55%, #0d3a7a 100%);
            background-color: #061336;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            opacity: .18;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='100' viewBox='0 0 56 100'%3E%3Cg fill='none' stroke='%2366b8ff' stroke-width='1'%3E%3Cpath d='M28 0 56 16 56 50 28 66 0 50 0 16Z'/%3E%3Cpath d='M28 66 56 82 56 100'/%3E%3Cpath d='M28 66 0 82 0 100'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 56px 100px;
        }

        .glow-dot { position: fixed; border-radius: 50%; background: #7cc3ff; filter: blur(1px); opacity: .55; animation: pulse 3.4s ease-in-out infinite; pointer-events: none; }
        .glow-dot.d1 { width: 10px; height: 10px; top: 14%; left: 10%; animation-delay: 0s; }
        .glow-dot.d2 { width: 7px; height: 7px; top: 70%; left: 20%; animation-delay: .6s; }
        .glow-dot.d3 { width: 9px; height: 9px; top: 30%; right: 12%; animation-delay: 1.1s; }
        .glow-dot.d4 { width: 6px; height: 6px; top: 80%; right: 22%; animation-delay: 1.7s; }
        @keyframes pulse { 0%, 100% { transform: scale(1); opacity: .35; } 50% { transform: scale(1.8); opacity: .85; } }

        .maint-card {
            position: relative;
            z-index: 1;
            width: min(560px, 100%);
            padding: 3rem 2.6rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .14);
            backdrop-filter: blur(6px);
            text-align: center;
            box-shadow: 0 30px 70px rgba(2, 10, 35, .55);
        }

        .maint-gear-stack { position: relative; width: 120px; height: 120px; margin: 0 auto 1.6rem; }
        .maint-gear-stack i { position: absolute; color: #7cc3ff; text-shadow: 0 0 24px rgba(124, 195, 255, .6); }
        .maint-gear-stack .gear-big { font-size: 5.5rem; top: 0; left: 0; animation: spin 7s linear infinite; }
        .maint-gear-stack .gear-small { font-size: 3rem; bottom: -4px; right: -8px; color: var(--accent, #f7941d); text-shadow: 0 0 20px rgba(247, 148, 29, .6); animation: spin-reverse 5s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes spin-reverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }

        .maint-card h1 { margin: 0 0 .6rem; color: #fff; font-size: 1.85rem; font-weight: 800; letter-spacing: -.01em; }
        .maint-card p { margin: 0 0 1.6rem; color: #c7d7f5; font-size: .95rem; line-height: 1.6; }

        .maint-progress { height: 6px; border-radius: 999px; background: rgba(255,255,255,.14); overflow: hidden; margin-bottom: 1.6rem; }
        .maint-progress span { display: block; height: 100%; width: 40%; border-radius: 999px; background: linear-gradient(90deg, #4fa8dc, #f7941d); animation: slide 1.8s ease-in-out infinite; }
        @keyframes slide { 0% { transform: translateX(-100%); } 100% { transform: translateX(350%); } }

        .maint-brand { display: inline-flex; align-items: center; gap: .55rem; padding: .55rem 1rem; border-radius: 999px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); }
        .maint-brand img { height: 22px; width: auto; }
        .maint-brand span { color: #e7eefc; font-size: .82rem; font-weight: 600; }

        @media (max-width: 480px) {
            .maint-card { padding: 2.2rem 1.6rem; }
        }
    </style>
</head>
<body>
    <span class="glow-dot d1"></span>
    <span class="glow-dot d2"></span>
    <span class="glow-dot d3"></span>
    <span class="glow-dot d4"></span>

    <div class="maint-card">
        <div class="maint-gear-stack" aria-hidden="true">
            <i class="bi bi-gear-fill gear-big"></i>
            <i class="bi bi-gear-fill gear-small"></i>
        </div>

        <h1>We'll be right back</h1>
        <p>We're performing some quick scheduled maintenance to make things better. Thanks for your patience — we'll be back online shortly.</p>

        <div class="maint-progress"><span></span></div>

        <div class="maint-brand">
            <img src="/images/brand-icon.png" alt="">
            <span>3G Computers</span>
        </div>
    </div>
</body>
</html>
