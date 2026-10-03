@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> <a href="{{ route('dashboard') }}">Home</a> <span>›</span> {{ $title }}</div>
@stop

@section('content')
    <div class="module-hub-heading"><h1>{{ $title }}</h1><p>Pick a feature below to get started.</p></div>

    <div class="module-grid-lg">
        @forelse ($items as $item)
            <a href="{{ route($item['route']) }}" class="module-card-lg" style="--module-accent: {{ $accent }};">
                <i class="bi {{ $item['icon'] ?? 'bi-circle' }} module-icon-lg"></i>
                <span class="module-title-lg">{{ $item['text'] }}</span>
            </a>
        @empty
            <p>No features configured for this module yet.</p>
        @endforelse
    </div>
@stop
