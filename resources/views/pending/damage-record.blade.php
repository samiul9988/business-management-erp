@extends('adminlte::page')

@section('title', 'Product Damage Record')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Pending Module <span>›</span> Damage Record</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Product Damage Record</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Sl</th><th>Invoice</th><th>Date</th><th>Product Name</th><th>Quantity</th><th>Damage Rate</th><th>Damage Amount</th><th>Description</th><th>Added By</th></tr></thead>
                <tbody>
                    @forelse ($damages as $index => $damage)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $damage->invoice_no }}</td>
                            <td>{{ $damage->damage_date->format('d/m/Y') }}</td>
                            <td>{{ $damage->product_name }}</td>
                            <td>{{ number_format($damage->quantity, 2) }}</td>
                            <td>{{ number_format($damage->rate, 2) }}</td>
                            <td>{{ number_format($damage->amount, 2) }}</td>
                            <td>{{ $damage->description ?? '-' }}</td>
                            <td>{{ $damage->user?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9">No damage records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
