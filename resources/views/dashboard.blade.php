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
            ['title' => 'Sales Module', 'description' => 'Create sales, manage customers and generate invoices.', 'icon' => 'bi-currency-dollar', 'class' => 'module-blue'],
            ['title' => 'Purchase Module', 'description' => 'Manage purchases, suppliers and stock.', 'icon' => 'bi-cart3', 'class' => 'module-green'],
            ['title' => 'Accounts Module', 'description' => 'Track accounts, expenses and financial reports.', 'icon' => 'bi-file-earmark-text', 'class' => 'module-purple'],
            ['title' => 'Warranty Module', 'description' => 'Manage product warranties and service details.', 'icon' => 'bi-shield-fill-check', 'class' => 'module-orange'],
            ['title' => 'Repair Module', 'description' => 'Handle product repairs and service tracking.', 'icon' => 'bi-wrench-adjustable', 'class' => 'module-pink'],
            ['title' => 'Reports Module', 'description' => 'View and print all kinds of reports.', 'icon' => 'bi-file-earmark-bar-graph', 'class' => 'module-teal'],
            ['title' => 'HR & Payroll', 'description' => 'Manage employee records and payroll.', 'icon' => 'bi-people-fill', 'class' => 'module-violet'],
            ['title' => 'Administration', 'description' => 'System settings and user management.', 'icon' => 'bi-gear-fill', 'class' => 'module-sky'],
            ['title' => 'Business Monitor', 'description' => 'Real-time business overview and statistics.', 'icon' => 'bi-bar-chart-line-fill', 'class' => 'module-cyan'],
            ['title' => 'LogOut', 'description' => 'Safely sign out from the system.', 'icon' => 'bi-power', 'class' => 'module-slate'],
        ];
    @endphp

    <form method="GET" action="{{ route('search') }}" class="dashboard-smart-search">
        <i class="bi bi-search"></i>
        <input type="text" name="q" placeholder="Search by phone number, barcode or serial number..." autocomplete="off" required>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <div class="module-grid">
        @foreach ($modules as $module)
            <a href="#" class="module-card {{ $module['class'] }}">
                <span class="module-icon"><i class="bi {{ $module['icon'] }}"></i></span>
                <span class="module-title">{{ $module['title'] }}</span>
                <span class="module-description">{{ $module['description'] }}</span>
                <span class="module-arrow"><i class="bi bi-arrow-right"></i></span>
            </a>
        @endforeach
    </div>
@stop
