@extends('adminlte::page')

@section('title', 'Employee Wise Sales')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> Employee Wise Sales</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-graph-up-arrow"></i> Employee Wise Top Sales</h2></div>
        <form method="GET" action="{{ route('sales.employee-sales') }}" class="record-filter-card">
            <div class="record-filter-field"><label for="from">From</label><input id="from" name="from" type="date" value="{{ $filters['from'] }}"></div>
            <div class="record-filter-field"><label for="to">To</label><input id="to" name="to" type="date" value="{{ $filters['to'] }}"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Filter</button>
        </form>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Rank</th><th>Employee</th><th>Invoices</th><th>Total Sales</th><th>Paid</th><th>Due</th></tr></thead>
                <tbody>
                    @forelse ($staff as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->user_name }}</td>
                            <td>{{ $row->invoice_count }}</td>
                            <td><strong>{{ number_format((float) $row->total_sales, 2) }}</strong></td>
                            <td>{{ number_format((float) $row->total_paid, 2) }}</td>
                            <td>{{ number_format((float) $row->total_due, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No sales found in this date range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
