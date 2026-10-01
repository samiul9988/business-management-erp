@extends('adminlte::page')

@section('title', $activeItem['title'] ?? 'Sales Module')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> {{ $activeItem['title'] ?? 'Overview' }}</div>
@stop

@section('content')
    <div class="sales-module-shell">
        <div class="sales-module-heading">
            <h1>{{ $activeItem['title'] ?? 'Sales Module' }}</h1>
            <p>{{ $activeItem['description'] ?? 'Manage sales, customers, invoices, returns, stock and quotations from one place.' }}</p>
        </div>

        <div class="sales-tile-grid">
            @foreach ($sections as $key => $item)
                <a href="{{ route($item['route']) }}" class="sales-tile {{ $activeSection === $key ? 'is-active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['title'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@stop
