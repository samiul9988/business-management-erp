@extends('adminlte::page')

@section('title', 'Sales Invoice')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> Sales Invoice</div>
@stop

@section('content')
    <div class="sales-module-shell">
        <div class="sales-module-heading">
            <h1>Sales Invoice</h1>
            <p>Find a sale by invoice number and print or download the invoice.</p>
        </div>

        <form method="GET" action="{{ route('sales.invoice') }}" class="record-filter-card">
            <div class="record-filter-field record-search"><label for="invoice_no">Invoice No</label><input id="invoice_no" name="invoice_no" value="{{ $searched }}" placeholder="Enter invoice no" autofocus></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Find Invoice</button>
        </form>

        @if ($searched && ! $sale)
            <div class="alert alert-warning">No sale found for invoice "{{ $searched }}".</div>
        @endif

        @if ($sale)
            <div class="record-table-card">
                <div class="record-table-heading"><h2><i class="bi bi-receipt"></i> Invoice {{ $sale->invoice_no }}</h2><button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print</button></div>
                <div class="record-summary">
                    <div><small>Date</small><strong>{{ $sale->sale_date->format('d/m/Y') }}</strong></div>
                    <div><small>Customer</small><strong>{{ $sale->customer_name }}</strong></div>
                    <div><small>Mobile</small><strong>{{ $sale->customer_mobile ?: '-' }}</strong></div>
                    <div><small>Total</small><strong>{{ number_format((float) $sale->total, 2) }}</strong></div>
                    <div><small>Paid</small><strong>{{ number_format((float) $sale->paid, 2) }}</strong></div>
                    <div class="due-summary"><small>Due</small><strong>{{ number_format((float) $sale->due, 2) }}</strong></div>
                </div>
                <div class="table-responsive">
                    <table class="sales-record-table">
                        <thead><tr><th>Product</th><th>Qty</th><th>Rate</th><th>Discount</th><th>Total</th></tr></thead>
                        <tbody>
                            @foreach ($sale->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                    <td>{{ number_format((float) $item->rate, 2) }}</td>
                                    <td>{{ number_format((float) $item->discount_amount, 2) }}</td>
                                    <td class="amount-cell">{{ number_format((float) $item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@stop
