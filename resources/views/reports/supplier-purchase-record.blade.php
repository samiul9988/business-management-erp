@extends('adminlte::page')

@section('title', 'Supplier Purchase Record')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Reports <span>›</span> Supplier Purchase Record</div>
@stop

@section('content')
    <section class="admin-crud-card" style="margin-bottom:1rem; max-width:500px;">
        <div class="sales-card-heading"><h2><i class="bi bi-search"></i> Select Supplier</h2></div>
        <form method="GET" action="{{ route('supplier-purchase-record.index') }}" class="admin-crud-form">
            <div class="field"><label>Supplier</label><select name="supplier_id" onchange="this.form.submit()"><option value="">Select Supplier</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected($supplier && $supplier->id === $s->id)>{{ $s->supplier_code }} - {{ $s->name }}</option>@endforeach</select></div>
        </form>
    </section>
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Purchase Record @if ($supplier) — {{ $supplier->name }} @endif</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Invoice</th><th>Date</th><th>Product</th><th>Qty</th><th>Rate</th><th>Amount</th><th>Total</th><th>Due</th></tr></thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        @foreach ($purchase->items as $item)
                            <tr>
                                <td>{{ $purchase->invoice_no }}</td>
                                <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ number_format($item->quantity, 2) }}</td>
                                <td>{{ number_format($item->rate, 2) }}</td>
                                <td>{{ number_format($item->total, 2) }}</td>
                                <td>{{ number_format($purchase->total, 2) }}</td>
                                <td>{{ number_format($purchase->due, 2) }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="8">{{ $supplier ? 'No purchases recorded for this supplier.' : 'Select a supplier to view their purchase history.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
