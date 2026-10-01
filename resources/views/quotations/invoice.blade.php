@extends('adminlte::page')

@section('title', 'Quotation Invoice')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> Quotation Invoice</div>
@stop

@section('content')
    <div class="sales-module-shell">
        <div class="sales-module-heading">
            <h1>Quotation Invoice</h1>
            <p>Find a saved quotation by number and print it for the customer.</p>
        </div>

        <form method="GET" action="{{ route('quotations.invoice.index') }}" class="record-filter-card">
            <div class="record-filter-field record-search"><label for="quotation_no">Quotation No</label><input id="quotation_no" name="quotation_no" value="{{ $searched }}" placeholder="Enter quotation no" autofocus></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Find Quotation</button>
        </form>

        @if ($searched && ! $quotation)
            <div class="alert alert-warning">No quotation found for "{{ $searched }}".</div>
        @endif

        @if ($quotation)
            <div class="record-table-card">
                <div class="record-table-heading"><h2><i class="bi bi-file-earmark-text"></i> Quotation {{ $quotation->quotation_no }}</h2><button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print</button></div>
                <div class="record-summary">
                    <div><small>Date</small><strong>{{ $quotation->quotation_date->format('d/m/Y') }}</strong></div>
                    <div><small>Valid Until</small><strong>{{ $quotation->valid_until?->format('d/m/Y') ?? '-' }}</strong></div>
                    <div><small>Customer</small><strong>{{ $quotation->customer_name }}</strong></div>
                    <div><small>Mobile</small><strong>{{ $quotation->customer_mobile ?: '-' }}</strong></div>
                    <div class="due-summary"><small>Total</small><strong>{{ number_format((float) $quotation->total, 2) }}</strong></div>
                </div>
                <div class="table-responsive">
                    <table class="sales-record-table">
                        <thead><tr><th>Product</th><th>Qty</th><th>Rate</th><th>Discount</th><th>Total</th></tr></thead>
                        <tbody>
                            @foreach ($quotation->items as $item)
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
