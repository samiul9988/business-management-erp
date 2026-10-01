@extends('adminlte::page')

@section('title', 'Serial Purchase Return')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Serial Purchase Return</div>
@stop

@section('content')
    <div class="serial-return-page">
        <div class="serial-return-heading"><div><span class="sales-kicker">AK COMPUTER, CCTV & LAPTOP</span><h1>Serial Purchase Return</h1><p>Find a serialized product by barcode and process its return to supplier.</p></div><div class="invoice-badge"><small>Return no</small><strong>{{ $nextReturn }}</strong></div></div>
        @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
        <form method="GET" action="{{ route('purchase.serial-return.create') }}" class="serial-search-box"><label for="serial-search">Serial No:</label><input id="serial-search" name="serial_no" value="{{ $serialNo }}" placeholder="Read Barcode..." autofocus><button type="submit" class="btn btn-secondary">Search</button></form>
        @if ($matchedReturn)<div class="alert alert-warning serial-match"><i class="bi bi-info-circle"></i> This serial was already returned under {{ $matchedReturn->return_no }} on {{ $matchedReturn->return_date->format('d/m/Y') }}.</div>@endif
        <form method="POST" action="{{ route('purchase.serial-return.store') }}" class="serial-return-form">
            @csrf
            <input type="hidden" name="serial_no" value="{{ $serialNo }}">
            <div class="serial-return-grid">
                <div class="serial-field"><label for="invoice_no">Invoice No</label><input id="invoice_no" name="invoice_no" placeholder="Optional invoice no"></div>
                <div class="serial-field"><label for="supplier_name">Supplier</label><input id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required></div>
                <div class="serial-field"><label for="product_code">Product Code</label><input id="product_code" name="product_code" value="{{ old('product_code') }}"></div>
                <div class="serial-field"><label for="product_name">Product Name</label><input id="product_name" name="product_name" value="{{ old('product_name') }}" required></div>
                <div class="serial-field"><label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" min=".01" step=".01" value="{{ old('quantity', 1) }}" required></div>
                <div class="serial-field"><label for="rate">Return Rate</label><input id="rate" name="rate" type="number" min="0" step=".01" value="{{ old('rate', 0) }}" required></div>
                <div class="serial-field serial-date"><label for="return_date">Return Date</label><input id="return_date" name="return_date" type="date" value="{{ old('return_date', now()->toDateString()) }}" required></div>
                <div class="serial-field serial-reason"><label for="reason">Return Reason</label><textarea id="reason" name="reason" rows="2" placeholder="Defective, warranty, wrong item...">{{ old('reason') }}</textarea></div>
            </div>
            <div class="serial-return-footer"><span><i class="bi bi-upc-scan"></i> Scan the serial number before saving the return.</span><button type="submit" class="btn btn-danger btn-lg"><i class="bi bi-arrow-return-left"></i> Save Serial Return</button></div>
        </form>
    </div>
@stop
