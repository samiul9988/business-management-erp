@extends('adminlte::page')

@section('title', 'Warranty Record')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Warranty Record</div>
@stop

@section('content')
    <div class="sales-record-page">
        <div class="record-titlebar"><div><span class="sales-kicker">3G COMPUTERS</span><h1>Warranty Record</h1><p>Search, review, and print warranty claim history.</p></div><button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print</button></div>
        <form method="GET" action="{{ route('warranty.record') }}" class="record-filter-card">
            <div class="record-filter-field"><label for="status">Status</label><select id="status" name="status"><option value="all" @selected($filters['status'] === 'all')>All</option><option value="pending" @selected($filters['status'] === 'pending')>Pending</option><option value="transferred_to_supplier" @selected($filters['status'] === 'transferred_to_supplier')>Transferred to Supplier</option><option value="received_from_supplier" @selected($filters['status'] === 'received_from_supplier')>Received from Supplier</option><option value="delivered_from_shop" @selected($filters['status'] === 'delivered_from_shop')>Delivered from Shop</option><option value="adjusted_by_supplier" @selected($filters['status'] === 'adjusted_by_supplier')>Adjusted by Supplier</option><option value="delivered" @selected($filters['status'] === 'delivered')>Delivered</option><option value="warranty_void" @selected($filters['status'] === 'warranty_void')>Warranty Void</option><option value="archived" @selected($filters['status'] === 'archived')>Archived</option></select></div>
            <div class="record-filter-field record-search"><label for="customer">Customer</label><input id="customer" name="customer" value="{{ $filters['customer'] }}" placeholder="Search here..."></div>
            <div class="record-filter-field"><label for="from">From</label><input id="from" name="from" type="date" value="{{ $filters['from'] }}"></div>
            <div class="record-filter-field"><label for="to">To</label><input id="to" name="to" type="date" value="{{ $filters['to'] }}"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="record-table-card"><div class="record-table-heading"><h2><i class="bi bi-table"></i> Warranty List</h2><span>{{ $warranties->total() }} record(s)</span></div><div class="table-responsive"><table class="sales-record-table"><thead><tr><th>Invoice No.</th><th>Date</th><th>Customer</th><th>Product</th><th>Serial No</th><th>Received By</th><th>Est. Delivery</th><th>Status</th></tr></thead><tbody>@forelse ($warranties as $warranty)<tr><td class="invoice-cell">{{ $warranty->invoice_no }}</td><td>{{ $warranty->warranty_date->format('d/m/Y') }}</td><td>{{ $warranty->customer_name }}</td><td>{{ $warranty->product_name ?? '-' }}</td><td>{{ $warranty->serial_no ?? '-' }}</td><td>{{ $warranty->receivedByEmployee?->name ?? '-' }}</td><td>{{ $warranty->estimated_delivery_date?->format('d/m/Y') ?? '-' }}</td><td><span class="status-badge">{{ ucfirst(str_replace('_', ' ', $warranty->warranty_status)) }}</span></td></tr>@empty<tr><td colspan="8" class="empty-records"><i class="bi bi-inbox"></i><strong>No warranty records found</strong><span>Adjust the date or search filters and try again.</span></td></tr>@endforelse</tbody></table></div><div class="record-pagination">{{ $warranties->links() }}</div></div>
    </div>
@stop
