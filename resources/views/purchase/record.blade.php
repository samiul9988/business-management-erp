@extends('adminlte::page')

@section('title', 'Purchase Record')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Purchase Record</div>
@stop

@section('content')
    <div class="sales-record-page">
        <div class="record-titlebar"><div><span class="sales-kicker">AK COMPUTER, CCTV & LAPTOP</span><h1>Purchase Record</h1><p>Search, review, and print completed purchase transactions.</p></div><button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print</button></div>
        <form method="GET" action="{{ route('purchase.record') }}" class="record-filter-card">
            <div class="record-filter-field record-search"><label for="supplier">Supplier</label><select id="supplier" name="supplier"><option value="">All Suppliers</option>@foreach ($suppliers as $supplier)<option value="{{ $supplier }}" @selected($filters['supplier'] === $supplier)>{{ $supplier }}</option>@endforeach</select></div>
            <div class="record-filter-field"><label for="invoice_no">Invoice No</label><input id="invoice_no" name="invoice_no" value="{{ $filters['invoice_no'] }}" placeholder="Enter invoice no"></div>
            <div class="record-filter-field"><label for="from">From</label><input id="from" name="from" type="date" value="{{ $filters['from'] }}"></div>
            <div class="record-filter-field"><label for="to">To</label><input id="to" name="to" type="date" value="{{ $filters['to'] }}"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="record-summary"><div><small>Invoices</small><strong>{{ $summary['invoices'] }}</strong></div><div><small>Total Quantity</small><strong>{{ number_format((float) $summary['quantity'], 2) }}</strong></div><div><small>Total</small><strong>{{ number_format((float) $summary['total'], 2) }}</strong></div><div><small>Paid</small><strong>{{ number_format((float) $summary['paid'], 2) }}</strong></div><div class="due-summary"><small>Due</small><strong>{{ number_format((float) $summary['due'], 2) }}</strong></div></div>
        <div class="record-table-card"><div class="record-table-heading"><h2><i class="bi bi-table"></i> Transaction List</h2><span>{{ $purchases->total() }} record(s)</span></div><div class="table-responsive"><table class="sales-record-table"><thead><tr><th>Invoice No.</th><th>Date</th><th>Supplier Name</th><th>Product Name</th><th>Rate</th><th>Quantity</th><th>Discount</th><th>Total</th><th>Paid</th><th>Due</th><th>Action</th></tr></thead><tbody>@forelse ($purchases as $purchase)<tr><td class="invoice-cell">{{ $purchase->invoice_no }}</td><td>{{ $purchase->purchase_date->format('d/m/Y') }}</td><td>{{ $purchase->supplier_name }}</td><td><div class="product-lines">@foreach ($purchase->items as $item)<span>{{ $item->product_name }}</span>@endforeach</div></td><td><div class="product-lines">@foreach ($purchase->items as $item)<span>{{ number_format((float) $item->rate, 2) }}</span>@endforeach</div></td><td><div class="product-lines">@foreach ($purchase->items as $item)<span>{{ number_format((float) $item->quantity, 2) }}</span>@endforeach</div></td><td>{{ number_format((float) $purchase->discount, 2) }}</td><td class="amount-cell">{{ number_format((float) $purchase->total, 2) }}</td><td>{{ number_format((float) $purchase->paid, 2) }}</td><td class="due-cell">{{ number_format((float) $purchase->due, 2) }}</td><td><button type="button" class="record-action" title="Print" onclick="window.print()"><i class="bi bi-printer"></i></button></td></tr>@empty<tr><td colspan="11" class="empty-records"><i class="bi bi-inbox"></i><strong>No purchase records found</strong><span>Adjust the date or search filters and try again.</span></td></tr>@endforelse</tbody></table></div><div class="record-pagination">{{ $purchases->links() }}</div></div>
    </div>
@stop
