<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'POS Express') }} — Sign In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    @php
        $company = \App\Models\CompanyProfile::first();
        $companyName = $company->name ?? config('app.name', 'POS Express');
        $companyAddress = $company->branch_address ?? null;
    @endphp

    <style>
        * { box-sizing: border-box; }
        .visually-hidden { position: absolute !important; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 2rem;
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

        /* Tiled hexagon pattern overlay */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            opacity: .18;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='100' viewBox='0 0 56 100'%3E%3Cg fill='none' stroke='%2366b8ff' stroke-width='1'%3E%3Cpath d='M28 0 56 16 56 50 28 66 0 50 0 16Z'/%3E%3Cpath d='M28 66 56 82 56 100'/%3E%3Cpath d='M28 66 0 82 0 100'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 56px 100px;
        }

        /* Soft glowing dots, purely decorative */
        .glow-dot { position: fixed; border-radius: 50%; background: #7cc3ff; filter: blur(1px); opacity: .55; animation: pulse 3.4s ease-in-out infinite; pointer-events: none; }
        .glow-dot.d1 { width: 10px; height: 10px; top: 14%; left: 10%; animation-delay: 0s; }
        .glow-dot.d2 { width: 7px; height: 7px; top: 70%; left: 20%; animation-delay: .6s; }
        .glow-dot.d3 { width: 9px; height: 9px; top: 30%; right: 12%; animation-delay: 1.1s; }
        .glow-dot.d4 { width: 6px; height: 6px; top: 80%; right: 22%; animation-delay: 1.7s; }
        .glow-dot.d5 { width: 8px; height: 8px; top: 50%; left: 48%; animation-delay: 2.2s; }
        @keyframes pulse { 0%, 100% { transform: scale(1); opacity: .35; } 50% { transform: scale(1.8); opacity: .85; } }

        .login-headline {
            position: relative;
            z-index: 1;
            text-align: center;
            margin: 0;
            color: #fff;
            font-size: clamp(1.4rem, 3.4vw, 2.1rem);
            font-weight: 700;
            text-shadow: 0 2px 18px rgba(50, 140, 255, .55);
        }

        .typewriter {
            display: inline-block;
            overflow: hidden;
            white-space: nowrap;
            border-right: 3px solid #7cc3ff;
            width: 0;
            animation: typing 2.6s steps(38, end) forwards, blink .75s step-end infinite;
        }
        @keyframes typing { from { width: 0; } to { width: 38ch; } }
        @keyframes blink { 50% { border-color: transparent; } }

        .auth-card {
            position: relative;
            z-index: 1;
            display: flex;
            width: min(960px, 100%);
            min-height: 480px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(2, 10, 35, .55);
        }

        .auth-brand-panel {
            flex: 0 0 42%;
            background: linear-gradient(165deg, #0d2d72 0%, #123a8f 100%);
            color: #fff;
            padding: 2.4rem 2.2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .auth-logo-badge {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 26px rgba(0, 0, 0, .25);
            margin-bottom: 1rem;
        }
        .auth-logo-badge img { max-width: 72%; max-height: 72%; width: auto; height: auto; object-fit: contain; }

        .auth-brand-panel h1 {
            font-size: 1.2rem;
            line-height: 1.35;
            margin: .2rem 0 1.4rem;
            font-weight: 700;
        }

        .auth-address-box {
            margin-top: auto;
            width: 100%;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 10px;
            padding: .9rem 1rem;
            font-size: .82rem;
            line-height: 1.55;
            text-align: left;
        }
        .auth-address-box strong { display: inline-flex; align-items: center; gap: .35rem; margin-bottom: .25rem; }

        .auth-divider-badge {
            position: absolute;
            right: -34px;
            top: 50%;
            transform: translateY(-50%);
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #123a8f;
            border: 5px solid #fff;
            z-index: 2;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .2);
        }

        .auth-form-panel {
            flex: 1;
            background: #fff;
            padding: 3rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-form-panel h2 {
            text-align: center;
            color: #1b2a4a;
            font-size: 1.5rem;
            margin: 0 0 1.8rem;
            font-weight: 700;
        }

        .auth-field { margin-bottom: 1.1rem; }
        .auth-field input {
            width: 100%;
            padding: .85rem 1.1rem;
            border: 1px solid #d7dee8;
            border-radius: 10px;
            font-size: .95rem;
            color: #26324a;
            background: #f8fafc;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .auth-field input:focus { border-color: #4b9ce2; box-shadow: 0 0 0 3px rgba(75, 156, 226, .18); background: #fff; }
        .auth-field .field-error { display: block; margin-top: .35rem; color: #d64545; font-size: .78rem; }

        .auth-submit {
            width: 100%;
            padding: .9rem;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(120deg, #4fa8dc, #3b8fc4);
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: filter .15s ease;
        }
        .auth-submit:hover { filter: brightness(1.06); }

        .auth-status { margin-bottom: 1rem; padding: .7rem .9rem; border-radius: 8px; background: #e9f8ee; color: #1d7a42; font-size: .85rem; }

        @media (max-width: 760px) {
            .auth-card { flex-direction: column; }
            .auth-brand-panel { flex: none; padding: 2rem 1.5rem; }
            .auth-divider-badge { display: none; }
            .auth-form-panel { padding: 2rem 1.6rem; }
        }
    </style>
</head>
<body>
    <span class="glow-dot d1"></span>
    <span class="glow-dot d2"></span>
    <span class="glow-dot d3"></span>
    <span class="glow-dot d4"></span>
    <span class="glow-dot d5"></span>

    <h1 class="login-headline"><span class="typewriter">Welcome to Online POS Accounting Software</span></h1>

    <div class="auth-card">
        <div class="auth-brand-panel">
            <div class="auth-logo-badge" aria-hidden="true">
                <img src="{{ asset('images/brand-icon.png') }}" alt="">
            </div>

            <h1>{{ $companyName }}</h1>

            @if ($companyAddress)
                <div class="auth-address-box">
                    <strong><i class="bi bi-geo-alt-fill"></i> Address</strong>
                    {{ $companyAddress }}
                </div>
            @endif

            <span class="auth-divider-badge" aria-hidden="true"></span>
        </div>

        <div class="auth-form-panel">
            <h2>Sign In Form</h2>

            @session('status')
                <div class="auth-status">{{ $value }}</div>
            @endsession

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="auth-field">
                    <label for="email" class="visually-hidden">User Name</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="User Name" required autofocus autocomplete="username">
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-field">
                    <label for="password" class="visually-hidden">Password</label>
                    <input id="password" type="password" name="password" placeholder="Password" required autocomplete="current-password">
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="auth-submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
