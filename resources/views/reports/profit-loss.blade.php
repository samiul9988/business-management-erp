@extends('adminlte::page')

@section('title', 'Profit & Loss Report')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Reports Module <span>›</span> Profit & Loss Report</div>
@stop

@section('content')
    <form method="GET" action="{{ route('profit-loss.index') }}" class="record-filter-card">
        <div class="record-filter-field"><label for="from">From</label><input id="from" name="from" type="date" value="{{ $filters['from'] }}"></div>
        <div class="record-filter-field"><label for="to">To</label><input id="to" name="to" type="date" value="{{ $filters['to'] }}"></div>
        <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
    </form>
    <section class="admin-crud-card" style="margin-top:1rem; max-width:640px;">
        <div class="sales-card-heading"><h2><i class="bi bi-graph-up"></i> Profit & Loss Summary</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <tbody>
                    <tr><td>Sales Total</td><td style="text-align:right;">{{ number_format($summary['salesTotal'], 2) }}</td></tr>
                    <tr><td>Repair Income</td><td style="text-align:right;">{{ number_format($summary['repairIncome'], 2) }}</td></tr>
                    <tr><td>Purchase Total (Cost)</td><td style="text-align:right;">- {{ number_format($summary['purchaseTotal'], 2) }}</td></tr>
                    <tr><td>Repair Parts Cost</td><td style="text-align:right;">- {{ number_format($summary['repairPartsCost'], 2) }}</td></tr>
                    <tr style="font-weight:700;"><td>Gross Profit</td><td style="text-align:right;">{{ number_format($summary['grossProfit'], 2) }}</td></tr>
                    <tr><td>Damage Loss</td><td style="text-align:right;">- {{ number_format($summary['damageLoss'], 2) }}</td></tr>
                    <tr><td>Salary Expense</td><td style="text-align:right;">- {{ number_format($summary['salaryExpense'], 2) }}</td></tr>
                    <tr><td>Other Cash Expense</td><td style="text-align:right;">- {{ number_format($summary['cashExpense'], 2) }}</td></tr>
                    <tr style="font-weight:700; font-size:1.05rem; color: {{ $summary['netProfit'] >= 0 ? '#25843d' : '#d54444' }};"><td>Net Profit</td><td style="text-align:right;">{{ number_format($summary['netProfit'], 2) }}</td></tr>
                </tbody>
            </table>
        </div>
    </section>
@stop
