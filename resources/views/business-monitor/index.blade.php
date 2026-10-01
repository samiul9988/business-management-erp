@extends('adminlte::page')

@section('title', $activeItem['title'] ?? 'Business Monitor')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Business Monitor <span>›</span> {{ $activeItem['title'] ?? 'Overview' }}</div>
@stop

@section('content')
    <div class="sales-module-shell">
        <div class="sales-module-heading">
            <h1>{{ $activeItem['title'] ?? 'Business Monitor' }}</h1>
            <p>{{ $activeItem['description'] ?? 'Keep an eye on sales, profit, stock, and staff activity in real time.' }}</p>
        </div>

        @if ($activeItem)
            <div class="sales-action-panel">
                <div class="sales-action-icon"><i class="bi {{ $activeItem['icon'] }}"></i></div>
                <div>
                    <h2>{{ $activeItem['title'] }}</h2>
                    <p>{{ $activeItem['description'] }}</p>
                    <div class="sales-form-row">
                        <label for="monitor-search">Search or filter</label>
                        <input id="monitor-search" type="search" placeholder="Enter branch, date range, or keyword">
                        <button type="button" class="btn btn-primary"><i class="bi bi-arrow-right"></i> Continue</button>
                    </div>
                </div>
            </div>
        @endif

        <div class="sales-tile-grid">
            @foreach ($sections as $key => $item)
                <a href="{{ route('business-monitor.section', ['section' => $key]) }}" class="sales-tile {{ $activeSection === $key ? 'is-active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['title'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@stop
