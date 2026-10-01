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
            <a href="{{ $dashboardUrl }}" class="pos-brand-mark"><span><i class="bi bi-cart3"></i></span> POS <strong>Express</strong></a>
            <div class="pos-auth-copy">
                <span class="dashboard-eyebrow">Smart business management</span>
                <h1>Everything you need<br>to run your business<br><em>better.</em></h1>
                <p>Sales, purchases, repairs, accounts and more — all in one simple POS platform.</p>
            </div>
            <div class="pos-auth-orbit orbit-one"></div>
            <div class="pos-auth-orbit orbit-two"></div>
            <i class="bi bi-bar-chart-line-fill pos-auth-float float-one"></i>
            <i class="bi bi-receipt pos-auth-float float-two"></i>
        </section>

        <section class="pos-auth-panel">

        {{-- Logo --}}
        <h1 class="{{ $authType }}-logo">
            <a href="{{ $dashboardUrl }}">

                {{-- Logo Image --}}
                @if (config('adminlte.auth_logo.enabled', false))
                    <img src="{{ asset(config('adminlte.auth_logo.img.path')) }}"
                         alt="{{ config('adminlte.auth_logo.img.alt') }}"
                         @if (config('adminlte.auth_logo.img.class', null))
                            class="{{ config('adminlte.auth_logo.img.class') }}"
                         @endif
                         @if (config('adminlte.auth_logo.img.width', null))
                            width="{{ config('adminlte.auth_logo.img.width') }}"
                         @endif
                         @if (config('adminlte.auth_logo.img.height', null))
                            height="{{ config('adminlte.auth_logo.img.height') }}"
                         @endif>
                @else
                    <img src="{{ asset(config('adminlte.logo_img')) }}"
                         alt="{{ config('adminlte.logo_img_alt') }}" height="50">
                @endif

                {{-- Logo Label --}}
                {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}

            </a>
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
