@extends('adminlte::page')

@section('title', 'Pending Sales Record')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Pending Module <span>›</span> Pending Sales Record</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-hourglass-split"></i> Pending Sales Record</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Invoice No.</th><th>Date</th><th>Customer Name</th><th>Saved By</th><th>Sub Total</th><th>VAT</th><th>Discount</th><th>Total</th><th>Paid</th><th>Due</th></tr></thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                            <td>{{ $sale->customer_name }}</td>
                            <td>{{ $sale->user?->name ?? '-' }}</td>
                            <td>{{ number_format($sale->subtotal, 2) }}</td>
                            <td>{{ number_format($sale->vat, 2) }}</td>
                            <td>{{ number_format($sale->discount, 2) }}</td>
                            <td>{{ number_format($sale->total, 2) }}</td>
                            <td>{{ number_format($sale->paid, 2) }}</td>
                            <td>{{ number_format($sale->due, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10">No pending sales found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
