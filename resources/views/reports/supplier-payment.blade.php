@extends('adminlte::page')

@section('title', 'Supplier Payment Reports')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Reports <span>›</span> Supplier Payment Report</div>
@stop

@section('content')
    <section class="admin-crud-card" style="margin-bottom:1rem; max-width:500px;">
        <div class="sales-card-heading"><h2><i class="bi bi-search"></i> Select Supplier</h2></div>
        <form method="GET" action="{{ route('supplier-payment-report.index') }}" class="admin-crud-form">
            <div class="field"><label>Supplier</label><select name="supplier_id" onchange="this.form.submit()"><option value="">Select Supplier</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected($supplier && $supplier->id === $s->id)>{{ $s->supplier_code }} - {{ $s->name }}</option>@endforeach</select></div>
        </form>
    </section>
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Payment History @if ($supplier) — {{ $supplier->name }} @endif</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Date</th><th>Description</th><th>Type</th><th>Paid</th><th>Discount</th><th>Balance</th></tr></thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td>{{ $payment->description ?? '-' }}</td>
                            <td>{{ ucfirst($payment->transaction_type) }} ({{ ucfirst($payment->payment_type) }})</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ number_format($payment->discount, 2) }}</td>
                            <td>{{ number_format($payment->running_balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">{{ $supplier ? 'No payments recorded for this supplier.' : 'Select a supplier to view their payment history.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
