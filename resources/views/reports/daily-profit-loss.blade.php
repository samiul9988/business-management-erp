@extends('adminlte::page')

@section('title', 'Daily Profit & Loss')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Reports Module <span>›</span> Daily Profit & Loss</div>
@stop

@section('content')
    <form method="GET" action="{{ route('daily-profit-loss.index') }}" class="record-filter-card">
        <div class="record-filter-field"><label for="month">Month</label><input id="month" name="month" type="month" value="{{ $month }}"></div>
        <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
    </form>
    <section class="admin-crud-card" style="margin-top:1rem;">
        <div class="sales-card-heading"><h2><i class="bi bi-graph-up"></i> Daily Profit & Loss — {{ $month }}</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Date</th><th>Sales</th><th>Repair Income</th><th>Purchase</th><th>Expenses</th><th>Net Profit</th></tr></thead>
                <tbody>
                    @forelse ($days as $day)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($day['date'])->format('d/m/Y') }}</td>
                            <td>{{ number_format($day['summary']['salesTotal'], 2) }}</td>
                            <td>{{ number_format($day['summary']['repairIncome'], 2) }}</td>
                            <td>{{ number_format($day['summary']['purchaseTotal'], 2) }}</td>
                            <td>{{ number_format($day['summary']['damageLoss'] + $day['summary']['salaryExpense'] + $day['summary']['cashExpense'], 2) }}</td>
                            <td style="color: {{ $day['summary']['netProfit'] >= 0 ? '#25843d' : '#d54444' }}; font-weight:600;">{{ number_format($day['summary']['netProfit'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No data for this month.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
