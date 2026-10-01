@extends('adminlte::page')

@section('title', 'Sales Record')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Record</div>
@stop

@section('content')
    <div class="sales-record-page">
        <div class="record-titlebar"><div><span class="sales-kicker">AK COMPUTER, CCTV & LAPTOP</span><h1>Sales Record</h1><p>Search, review, and print completed sales transactions.</p></div><button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print</button></div>
        <form method="GET" action="{{ route('sales.record') }}" class="record-filter-card">
            <div class="record-filter-field"><label for="search_type">Search Type</label><select id="search_type" name="search_type"><option value="all" @selected($filters['search_type'] === 'all')>All</option><option value="customer" @selected($filters['search_type'] === 'customer')>By Customer</option><option value="invoice" @selected($filters['search_type'] === 'invoice')>By Invoice</option></select></div>
            <div class="record-filter-field"><label for="record_type">Record Type</label><select id="record_type" name="record_type"><option value="without_details" @selected($filters['record_type'] === 'without_details')>Without Details</option><option value="with_details" @selected($filters['record_type'] === 'with_details')>With Details</option></select></div>
            <div class="record-filter-field"><label for="status">Status</label><select id="status" name="status"><option value="all" @selected($filters['status'] === 'all')>All</option><option value="approved" @selected($filters['status'] === 'approved')>Approved</option><option value="pending" @selected($filters['status'] === 'pending')>Pending</option></select></div>
            <div class="record-filter-field record-search"><label for="customer">{{ $filters['search_type'] === 'invoice' ? 'Invoice No' : 'Customer' }}</label>@if ($filters['search_type'] === 'customer')<select id="customer" name="customer"><option value="">Select Customer</option>@foreach ($customers as $customer)<option value="{{ $customer }}" @selected($filters['customer'] === $customer)>{{ $customer }}</option>@endforeach</select>@elseif ($filters['search_type'] === 'invoice')<input id="customer" name="invoice_no" value="{{ $filters['invoice_no'] }}" placeholder="Enter invoice no">@else<input id="customer" name="customer" value="{{ $filters['customer'] }}" placeholder="Search here...">@endif</div>
            <div class="record-filter-field"><label for="from">From</label><input id="from" name="from" type="date" value="{{ $filters['from'] }}"></div>
            <div class="record-filter-field"><label for="to">To</label><input id="to" name="to" type="date" value="{{ $filters['to'] }}"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="record-summary"><div><small>Invoices</small><strong>{{ $summary['invoices'] }}</strong></div><div><small>Total Quantity</small><strong>{{ number_format((float) $summary['quantity'], 2) }}</strong></div><div><small>Total</small><strong>{{ number_format((float) $summary['total'], 2) }}</strong></div><div><small>Paid</small><strong>{{ number_format((float) $summary['paid'], 2) }}</strong></div><div class="due-summary"><small>Due</small><strong>{{ number_format((float) $summary['due'], 2) }}</strong></div></div>
        <div class="record-table-card"><div class="record-table-heading"><h2><i class="bi bi-table"></i> Transaction List</h2><span>{{ $sales->total() }} record(s)</span></div><div class="table-responsive"><table class="sales-record-table"><thead><tr><th>Invoice No.</th><th>Date</th><th>Customer Name</th><th>Product Name</th><th>Price</th><th>Quantity</th><th>Discount</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Action</th></tr></thead><tbody>@forelse ($sales as $sale)<tr><td class="invoice-cell">{{ $sale->invoice_no }}</td><td>{{ $sale->sale_date->format('d/m/Y') }}</td><td>{{ $sale->customer_name }}</td><td><div class="product-lines">@foreach ($sale->items as $item)<span>{{ $item->product_name }}</span>@endforeach</div></td><td><div class="product-lines">@foreach ($sale->items as $item)<span>{{ number_format((float) $item->rate, 2) }}</span>@endforeach</div></td><td><div class="product-lines">@foreach ($sale->items as $item)<span>{{ number_format((float) $item->quantity, 2) }}</span>@endforeach</div></td><td>{{ number_format((float) $sale->discount, 2) }}</td><td class="amount-cell">{{ number_format((float) $sale->total, 2) }}</td><td>{{ number_format((float) $sale->paid, 2) }}</td><td class="due-cell">{{ number_format((float) $sale->due, 2) }}</td><td><span class="status-badge">Approved</span></td><td><button type="button" class="record-action" title="View"><i class="bi bi-eye"></i></button><button type="button" class="record-action" title="Print" onclick="window.print()"><i class="bi bi-printer"></i></button></td></tr>@empty<tr><td colspan="12" class="empty-records"><i class="bi bi-inbox"></i><strong>No sales records found</strong><span>Adjust the date or search filters and try again.</span></td></tr>@endforelse</tbody></table></div><div class="record-pagination">{{ $sales->links() }}</div></div>
    </div>
@stop

@push('js')
<script>
(() => {
    const searchType = document.querySelector('#search_type');
    searchType.addEventListener('change', () => {
        const form = searchType.closest('form');
        const customerField = form.querySelector('#customer');
        if (customerField) {
            customerField.focus();
        }
    });
})();
</script>
@endpush
