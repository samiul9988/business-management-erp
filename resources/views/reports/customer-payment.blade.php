@extends('adminlte::page')

@section('title', 'Customer Payment Reports')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Reports <span>›</span> Customer Payment Report</div>
@stop

@section('content')
    <section class="admin-crud-card" style="margin-bottom:1rem; max-width:500px;">
        <div class="sales-card-heading"><h2><i class="bi bi-search"></i> Select Customer</h2></div>
        <form method="GET" action="{{ route('customer-payment-report.index') }}" class="admin-crud-form">
            <div class="field"><label>Customer</label><select name="customer_id" onchange="this.form.submit()"><option value="">Select Customer</option>@foreach ($customers as $c)<option value="{{ $c->id }}" @selected($customer && $customer->id === $c->id)>{{ $c->customer_code }} - {{ $c->name }}</option>@endforeach</select></div>
        </form>
    </section>
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Payment History @if ($customer) — {{ $customer->name }} @endif</h2></div>
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
                        <tr><td colspan="6">{{ $customer ? 'No payments recorded for this customer.' : 'Select a customer to view their payment history.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
