@extends('adminlte::page')

@section('title', 'Quotation Record')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> Quotation Record</div>
@stop

@section('content')
    <div class="sales-record-page">
        <div class="record-titlebar"><div><span class="sales-kicker">AK COMPUTER, CCTV & LAPTOP</span><h1>Quotation Record</h1><p>Search and review saved customer quotations.</p></div></div>
        <form method="GET" action="{{ route('quotations.record') }}" class="record-filter-card">
            <div class="record-filter-field record-search"><label for="customer">Customer</label><input id="customer" name="customer" value="{{ $filters['customer'] }}" placeholder="Search customer..."></div>
            <div class="record-filter-field"><label for="quotation_no">Quotation No</label><input id="quotation_no" name="quotation_no" value="{{ $filters['quotation_no'] }}" placeholder="Enter quotation no"></div>
            <div class="record-filter-field"><label for="from">From</label><input id="from" name="from" type="date" value="{{ $filters['from'] }}"></div>
            <div class="record-filter-field"><label for="to">To</label><input id="to" name="to" type="date" value="{{ $filters['to'] }}"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="record-table-card">
            <div class="record-table-heading"><h2><i class="bi bi-table"></i> Quotation List</h2><span>{{ $quotations->total() }} record(s)</span></div>
            <div class="table-responsive">
                <table class="sales-record-table">
                    <thead><tr><th>Quotation No.</th><th>Date</th><th>Valid Until</th><th>Customer Name</th><th>Product Name</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($quotations as $quotation)
                            <tr>
                                <td class="invoice-cell">{{ $quotation->quotation_no }}</td>
                                <td>{{ $quotation->quotation_date->format('d/m/Y') }}</td>
                                <td>{{ $quotation->valid_until?->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $quotation->customer_name }}</td>
                                <td><div class="product-lines">@foreach ($quotation->items as $item)<span>{{ $item->product_name }}</span>@endforeach</div></td>
                                <td class="amount-cell">{{ number_format((float) $quotation->total, 2) }}</td>
                                <td><span class="status-badge">{{ ucfirst($quotation->status) }}</span></td>
                                <td><a href="{{ route('quotations.invoice.show', $quotation) }}" class="record-action" title="View"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty-records"><i class="bi bi-inbox"></i><strong>No quotations found</strong><span>Adjust the date or search filters and try again.</span></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="record-pagination">{{ $quotations->links() }}</div>
        </div>
    </div>
@stop
