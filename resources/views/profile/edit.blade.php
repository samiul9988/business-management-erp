@extends('adminlte::page')

@section('title', 'Profile')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Profile</div>
@stop

@section('content')
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> Profile updated successfully.</div>
    @endif
    @if (session('status') === 'password-updated')
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> Password updated successfully.</div>
    @endif

    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            @include('profile.partials.update-profile-information-form')
        </section>

        <section class="admin-crud-card">
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </section>
    </div>
@stop
