@extends('adminlte::page')

@section('title', 'Cash View')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Reports Module <span>›</span> Cash View</div>
@stop

@section('content')
    <div class="record-summary" style="grid-template-columns: 1fr;">
        <div><small>Current Cash Balance</small><strong style="font-size:1.4rem;">{{ number_format($currentBalance, 2) }}</strong></div>
    </div>
    <section class="admin-crud-card" style="margin-top:1rem;">
        <div class="sales-card-heading"><h2><i class="bi bi-cash"></i> Cash Movements</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Date</th><th>Description</th><th>In</th><th>Out</th><th>Balance</th></tr></thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($movement['date'])->format('d/m/Y') }}</td>
                            <td>{{ $movement['description'] ?? '-' }}</td>
                            <td style="color:#25843d;">{{ $movement['in'] > 0 ? number_format($movement['in'], 2) : '-' }}</td>
                            <td style="color:#d54444;">{{ $movement['out'] > 0 ? number_format($movement['out'], 2) : '-' }}</td>
                            <td style="font-weight:600;">{{ number_format($movement['balance'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No cash movements recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
