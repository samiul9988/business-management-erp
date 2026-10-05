@extends('adminlte::page')

@section('title', 'Sales Return Record')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Return Record</div>
@stop

@section('content')
    <div class="sales-record-page">
        <div class="record-titlebar"><div><span class="sales-kicker">3G COMPUTERS</span><h1>Sales Return Record</h1><p>Search, review, and print completed sales return transactions.</p></div><button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print</button></div>
        <form method="GET" action="{{ route('sales.return-record') }}" class="record-filter-card">
            <div class="record-filter-field record-search"><label for="customer">Customer</label><input id="customer" name="customer" value="{{ $filters['customer'] }}" placeholder="Search customer..."></div>
            <div class="record-filter-field"><label for="invoice_no">Invoice No</label><input id="invoice_no" name="invoice_no" value="{{ $filters['invoice_no'] }}" placeholder="Enter invoice no"></div>
            <div class="record-filter-field"><label for="from">From</label><input id="from" name="from" type="date" value="{{ $filters['from'] }}"></div>
            <div class="record-filter-field"><label for="to">To</label><input id="to" name="to" type="date" value="{{ $filters['to'] }}"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="record-summary"><div><small>Returns</small><strong>{{ $summary['count'] }}</strong></div><div><small>Total Returned</small><strong>{{ number_format((float) $summary['total'], 2) }}</strong></div></div>
        <div class="record-table-card">
            <div class="record-table-heading"><h2><i class="bi bi-table"></i> Return List</h2><span>{{ $returns->total() }} record(s)</span></div>
            <div class="table-responsive">
                <table class="sales-record-table">
                    <thead><tr><th>Return No.</th><th>Invoice No.</th><th>Date</th><th>Customer Name</th><th>Product Name</th><th>Quantity</th><th>Reason</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse ($returns as $return)
                            <tr>
                                <td class="invoice-cell">{{ $return->return_no }}</td>
                                <td>{{ $return->invoice_no }}</td>
                                <td>{{ $return->return_date->format('d/m/Y') }}</td>
                                <td>{{ $return->customer_name }}</td>
                                <td><div class="product-lines">@foreach ($return->items as $item)<span>{{ $item->product_name }}</span>@endforeach</div></td>
                                <td><div class="product-lines">@foreach ($return->items as $item)<span>{{ number_format((float) $item->quantity, 2) }}</span>@endforeach</div></td>
                                <td>{{ $return->reason ?: '-' }}</td>
                                <td class="amount-cell">{{ number_format((float) $return->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty-records"><i class="bi bi-inbox"></i><strong>No sales return records found</strong><span>Adjust the date or search filters and try again.</span></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="record-pagination">{{ $returns->links() }}</div>
        </div>
    </div>
@stop
