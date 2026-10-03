@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="dashboard-hero">
        <div>
            <span class="dashboard-eyebrow">Good {{ now()->format('A') === 'AM' ? 'Morning' : 'Evening' }},</span>
            <h1>{{ auth()->user()->name }}</h1>
            <p>Welcome to your POS Express dashboard</p>
            <div class="dashboard-meta">
                <span><i class="bi bi-diagram-3-fill"></i> Branch Access</span>
                <span><i class="bi bi-calendar3"></i> {{ now()->format('D, d M Y') }}</span>
                <span><i class="bi bi-clock-fill"></i> {{ now()->format('h:i:s A') }}</span>
            </div>
        </div>
        <div class="hero-illustration" aria-hidden="true">
            <i class="bi bi-display"></i>
            <i class="bi bi-cart3"></i>
            <i class="bi bi-receipt"></i>
        </div>
        <div class="hero-message">Smart POS<br><strong>for a Better<br>Tomorrow</strong></div>
    </div>
@stop

@section('content')
    @php
        $modules = [
            ['title' => 'Sales Module', 'icon' => 'bi-currency-dollar', 'accent' => '#2377ed', 'href' => route('modules.show', 'sales')],
            ['title' => 'Purchase Module', 'icon' => 'bi-cart3', 'accent' => '#3eb27c', 'href' => route('modules.show', 'purchase')],
            ['title' => 'Accounts Module', 'icon' => 'bi-file-earmark-text', 'accent' => '#7752df', 'href' => route('modules.show', 'accounts')],
            ['title' => 'Warranty Module', 'icon' => 'bi-shield-fill-check', 'accent' => '#ff851e', 'href' => route('modules.show', 'warranty')],
            ['title' => 'Repair Module', 'icon' => 'bi-wrench-adjustable', 'accent' => '#ed4d70', 'href' => route('modules.show', 'repair')],
            ['title' => 'Reports Module', 'icon' => 'bi-file-earmark-bar-graph', 'accent' => '#27aaa3', 'href' => route('modules.show', 'reports')],
            ['title' => 'HR & Payroll', 'icon' => 'bi-people-fill', 'accent' => '#7752df', 'href' => route('modules.show', 'hr-payroll')],
            ['title' => 'Administration', 'icon' => 'bi-gear-fill', 'accent' => '#607697', 'href' => route('modules.show', 'administration')],
            ['title' => 'Business Monitor', 'icon' => 'bi-bar-chart-line-fill', 'accent' => '#27aaa3', 'href' => route('business-monitor.index')],
            ['title' => 'LogOut', 'icon' => 'bi-power', 'accent' => '#607697', 'href' => '#', 'id' => 'dashboard-logout-link'],
        ];
    @endphp

    <div class="dashboard-brand-banner">
        <span class="dashboard-brand-icon"><i class="bi bi-cart3"></i></span>
        <span class="dashboard-brand-text">AK Computer, CCTV &amp; Laptop</span>
    </div>

    <form method="GET" action="{{ route('search') }}" class="dashboard-smart-search">
        <i class="bi bi-search"></i>
        <input type="text" name="q" placeholder="Search by phone number, barcode or serial number..." autocomplete="off" required>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <div class="module-grid-lg">
        @foreach ($modules as $module)
            <a href="{{ $module['href'] }}" @isset($module['id']) id="{{ $module['id'] }}" @endisset class="module-card-lg" style="--module-accent: {{ $module['accent'] }};">
                <i class="bi {{ $module['icon'] }} module-icon-lg"></i>
                <span class="module-title-lg">{{ $module['title'] }}</span>
            </a>
        @endforeach
    </div>

    <form id="dashboard-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
@stop

@push('js')
<script>
    document.getElementById('dashboard-logout-link')?.addEventListener('click', (event) => {
        event.preventDefault();
        document.getElementById('dashboard-logout-form').submit();
    });
</script>
@endpush
