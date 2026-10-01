@extends('adminlte::page')

@section('title', 'Top Customers')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> Top Customers</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-people-fill"></i> Top Customer List</h2></div>
        <form method="GET" action="{{ route('sales.top-customers') }}" class="record-filter-card">
            <div class="record-filter-field"><label for="from">From</label><input id="from" name="from" type="date" value="{{ $filters['from'] }}"></div>
            <div class="record-filter-field"><label for="to">To</label><input id="to" name="to" type="date" value="{{ $filters['to'] }}"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Filter</button>
        </form>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Rank</th><th>Customer Name</th><th>Mobile</th><th>Invoices</th><th>Total Spent</th><th>Due</th></tr></thead>
                <tbody>
                    @forelse ($customers as $index => $customer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $customer->customer_name }}</td>
                            <td>{{ $customer->customer_mobile ?: '-' }}</td>
                            <td>{{ $customer->invoice_count }}</td>
                            <td><strong>{{ number_format((float) $customer->total_spent, 2) }}</strong></td>
                            <td>{{ number_format((float) $customer->total_due, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No sales found in this date range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
