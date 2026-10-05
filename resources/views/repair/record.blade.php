@extends('adminlte::page')

@section('title', 'Repair Record')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Repair Record</div>
@stop

@section('content')
    <div class="sales-record-page">
        <div class="record-titlebar"><div><span class="sales-kicker">3G COMPUTERS</span><h1>Repair Record</h1><p>Search, review, and print completed repair jobs.</p></div><button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print</button></div>
        <form method="GET" action="{{ route('repair.record') }}" class="record-filter-card">
            <div class="record-filter-field"><label for="status">Status</label><select id="status" name="status"><option value="all" @selected($filters['status'] === 'all')>All</option><option value="pending" @selected($filters['status'] === 'pending')>Pending</option><option value="completed" @selected($filters['status'] === 'completed')>Completed</option><option value="non_completed" @selected($filters['status'] === 'non_completed')>Non-Completed</option><option value="delivered" @selected($filters['status'] === 'delivered')>Delivered</option><option value="transfer" @selected($filters['status'] === 'transfer')>Transfer</option><option value="received" @selected($filters['status'] === 'received')>Received</option></select></div>
            <div class="record-filter-field record-search"><label for="customer">Customer</label><input id="customer" name="customer" value="{{ $filters['customer'] }}" placeholder="Search here..."></div>
            <div class="record-filter-field"><label for="from">From</label><input id="from" name="from" type="date" value="{{ $filters['from'] }}"></div>
            <div class="record-filter-field"><label for="to">To</label><input id="to" name="to" type="date" value="{{ $filters['to'] }}"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="record-summary"><div><small>Invoices</small><strong>{{ $summary['invoices'] }}</strong></div><div><small>Sale Total</small><strong>{{ number_format((float) $summary['saleTotal'], 2) }}</strong></div><div><small>Purchase Total</small><strong>{{ number_format((float) $summary['purchaseTotal'], 2) }}</strong></div><div><small>Profit</small><strong>{{ number_format((float) $summary['profit'], 2) }}</strong></div><div class="due-summary"><small>Due</small><strong>{{ number_format((float) $summary['due'], 2) }}</strong></div></div>
        <div class="record-table-card"><div class="record-table-heading"><h2><i class="bi bi-table"></i> Repair List</h2><span>{{ $repairs->total() }} record(s)</span></div><div class="table-responsive"><table class="sales-record-table"><thead><tr><th>Invoice No.</th><th>Date</th><th>Exp. Del. Date</th><th>Customer Name</th><th>Assign To</th><th>Sale Total</th><th>Purchase Total</th><th>Paid</th><th>Due</th><th>Profit</th><th>Status</th></tr></thead><tbody>@forelse ($repairs as $repair)<tr><td class="invoice-cell">{{ $repair->invoice_no }}</td><td>{{ $repair->repair_date->format('d/m/Y') }}</td><td>{{ $repair->expected_delivery_date?->format('d/m/Y') ?? '-' }}</td><td>{{ $repair->customer_name }}</td><td>{{ $repair->assignedEmployee?->name ?? '-' }}</td><td class="amount-cell">{{ number_format((float) $repair->total, 2) }}</td><td>{{ number_format((float) $repair->parts_purchase_amount, 2) }}</td><td>{{ number_format((float) $repair->paid, 2) }}</td><td class="due-cell">{{ number_format((float) $repair->due, 2) }}</td><td>{{ number_format((float) $repair->total - (float) $repair->parts_purchase_amount, 2) }}</td><td><span class="status-badge">{{ ucfirst(str_replace('_', ' ', $repair->status)) }}</span></td></tr>@empty<tr><td colspan="11" class="empty-records"><i class="bi bi-inbox"></i><strong>No repair records found</strong><span>Adjust the date or search filters and try again.</span></td></tr>@endforelse</tbody></table></div><div class="record-pagination">{{ $repairs->links() }}</div></div>
    </div>
@stop
