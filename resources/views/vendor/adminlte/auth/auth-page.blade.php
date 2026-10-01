@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php
    $authType = $authType ?? 'login';
    $dashboardUrl = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home');
    $dashboardUrl = $layoutHelper->makeUrl($dashboardUrl);

    // The AdminLTE v4 authentication pages center their content by using the
    // '{login,register}-page' body class. The RTL direction and the color mode
    // are already setup on the <html> tag by the master layout.

    $bodyClasses = "{$authType}-page bg-body-secondary";
@endphp

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body'){{ $bodyClasses }}@stop

@section('body')
    <main class="pos-auth-shell {{ $authType }}-box">

        <section class="pos-auth-brand">
            <a href="{{ $dashboardUrl }}" class="pos-brand-mark"><span class="pos-sidebar-logo-icon"><i class="bi bi-cart3"></i></span> POS <strong>Express</strong></a>
            <div class="pos-auth-copy">
                <span class="dashboard-eyebrow">Smart business management</span>
                <h1>Everything you need<br>to run your business<br><em>better.</em></h1>
                <p>Sales, purchases, repairs, accounts and more — all in one simple POS platform.</p>
            </div>

            <ul class="pos-auth-features">
                <li><span class="pos-auth-feature-icon feature-blue"><i class="bi bi-cart-check-fill"></i></span><div><strong>Sales &amp; POS</strong><span>Fast, easy and secure sales process</span></div></li>
                <li><span class="pos-auth-feature-icon feature-green"><i class="bi bi-box-seam-fill"></i></span><div><strong>Purchases</strong><span>Manage suppliers and inventory</span></div></li>
                <li><span class="pos-auth-feature-icon feature-purple"><i class="bi bi-tools"></i></span><div><strong>Repairs</strong><span>Track and manage service &amp; repairs</span></div></li>
                <li><span class="pos-auth-feature-icon feature-orange"><i class="bi bi-bar-chart-fill"></i></span><div><strong>Accounts</strong><span>Complete financial control</span></div></li>
                <li><span class="pos-auth-feature-icon feature-teal"><i class="bi bi-people-fill"></i></span><div><strong>And more</strong><span>All in one platform</span></div></li>
            </ul>

            <div class="pos-auth-mockup" aria-hidden="true">
                <div class="pos-auth-mockup-screen">
                    <div class="pos-auth-mockup-sidebar">
                        <span class="pos-auth-mockup-dot"></span>
                        <span></span><span></span><span></span><span></span>
                    </div>
                    <div class="pos-auth-mockup-main">
                        <div class="pos-auth-mockup-cards">
                            <div class="mockup-card card-blue"><i class="bi bi-cash-coin"></i><strong>৳125,480</strong><small>Total Sales</small></div>
                            <div class="mockup-card card-green"><i class="bi bi-bag-check-fill"></i><strong>৳82,350</strong><small>Total Purchases</small></div>
                        </div>
                        <div class="pos-auth-mockup-chart"><svg viewBox="0 0 200 54" preserveAspectRatio="none"><polyline points="0,40 25,30 50,38 75,18 100,28 125,10 150,22 175,6 200,16"></polyline></svg></div>
                    </div>
                </div>
            </div>

            <ul class="pos-auth-trust">
                <li><i class="bi bi-shield-check"></i> Secure &amp; Reliable</li>
                <li><i class="bi bi-lightning-charge-fill"></i> Fast Performance</li>
                <li><i class="bi bi-cloud-check-fill"></i> Cloud Ready</li>
            </ul>
        </section>

        <section class="pos-auth-panel">

        {{-- Logo --}}
        <h1 class="{{ $authType }}-logo">
            <a href="{{ $dashboardUrl }}">
                <span class="pos-sidebar-logo-icon"><i class="bi bi-cart3"></i></span>
                <span>POS <b>Express</b></span>
            </a>
            <small>Smart business management</small>
        </h1>

        {{-- Card Box --}}
        <div class="card {{ config('adminlte.classes_auth_card', 'card-outline card-primary') }}">

            {{-- Card Header --}}
            @hasSection('auth_header')
                <div class="card-header {{ config('adminlte.classes_auth_header', '') }}">
                    <h3 class="card-title float-none text-center">
                        @yield('auth_header')
                    </h3>
                </div>
            @endif

            {{-- Card Body --}}
            <div class="card-body {{ $authType }}-card-body {{ config('adminlte.classes_auth_body', '') }}">
                @yield('auth_body')
            </div>

            {{-- Card Footer --}}
            @hasSection('auth_footer')
                <div class="card-footer {{ config('adminlte.classes_auth_footer', '') }}">
                    @yield('auth_footer')
                </div>
            @endif

        </div>

        <p class="pos-auth-footer">© {{ now()->year }} POS Express · Secure business platform</p>

        </section>
    </main>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
