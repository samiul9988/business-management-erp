@extends('adminlte::page')

@section('title', 'Site Maintenance')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> <a href="{{ route('dashboard') }}">Home</a> <span>›</span> Administration <span>›</span> Site Maintenance</div>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('status') }}</div>
    @endif

    <div class="admin-crud-layout" style="grid-template-columns: 1fr; max-width: 640px;">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-toggle-on"></i> Site Maintenance Mode</h2></div>

            <div class="maintenance-status-row">
                <div>
                    <span class="maintenance-status-badge {{ $active ? 'is-on' : 'is-off' }}">
                        <i class="bi {{ $active ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' }}"></i>
                        {{ $active ? 'Maintenance mode is ON' : 'Site is LIVE' }}
                    </span>
                    <p class="maintenance-status-desc">
                        @if ($active)
                            Visitors currently see the maintenance page instead of the application. This screen and the login page stay reachable so you can always switch it back off.
                        @else
                            Everyone can use the site normally right now.
                        @endif
                    </p>
                </div>

                <form method="POST" action="{{ route('maintenance.toggle', $key ? ['key' => $key] : []) }}">
                    @csrf
                    @if ($key)
                        <input type="hidden" name="key" value="{{ $key }}">
                    @endif
                    <button type="submit" class="maintenance-toggle-switch {{ $active ? 'is-on' : '' }}" aria-pressed="{{ $active ? 'true' : 'false' }}">
                        <span class="maintenance-toggle-knob"></span>
                    </button>
                </form>
            </div>

            <div class="maintenance-preview-link">
                <a href="{{ route('maintenance.preview') }}" target="_blank" class="btn btn-light btn-sm">
                    <i class="bi bi-eye"></i> Preview the maintenance page
                </a>
            </div>

            @unless (auth()->check())
                <div class="maintenance-bookmark-note">
                    <i class="bi bi-bookmark-star"></i>
                    Bookmark this exact page URL (with the <code>?key=...</code> in it) — it's the only way back in without logging in.
                </div>
            @endunless
        </section>
    </div>
@stop
