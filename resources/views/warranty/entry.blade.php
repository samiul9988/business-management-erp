@extends('adminlte::page')

@section('title', 'Warranty Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Warranty Module <span>›</span> Warranty Entry</div>
@stop

@section('content')
    <form method="POST" action="{{ route('warranty.entry.store') }}" class="pos-sales-entry" id="warranty-entry-form">
        @csrf
        <div class="sales-entry-toolbar">
            <div><span class="sales-kicker">3G COMPUTERS</span><h1>Warranty Entry</h1><p>Track a warranty claim from intake through delivery.</p></div>
            <div class="invoice-badge"><small>Invoice no</small><strong>{{ $nextInvoice }}</strong></div>
            <div class="date-field"><label for="warranty_date">Date</label><input id="warranty_date" name="warranty_date" type="date" value="{{ old('warranty_date', now()->toDateString()) }}" required></div>
        </div>
        @if (session('success')) <div class="alert alert-success sales-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
        <div class="sales-entry-layout">
            <section class="sales-entry-card sales-information-card" style="grid-column: 1 / -1;">
                <div class="sales-card-heading"><h2><i class="bi bi-person-vcard"></i> Customer & Product Information</h2></div>
                <div class="sales-entry-grid customer-grid">
                    <div><label for="customer_name">Customer Name</label><input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required></div>
                    <div><label for="customer_mobile">Mobile No</label><input id="customer_mobile" name="customer_mobile" value="{{ old('customer_mobile') }}"></div>
                    <div><label for="received_by_employee_id">Received By</label><select id="received_by_employee_id" name="received_by_employee_id"><option value="">-- Select --</option>@foreach ($employees as $employee)<option value="{{ $employee->id }}" @selected(old('received_by_employee_id') == $employee->id)>{{ $employee->name }}</option>@endforeach</select></div>
                    <div><label for="warranty_status">Warranty Status</label><select id="warranty_status" name="warranty_status" required><option value="pending" @selected(old('warranty_status', 'pending') === 'pending')>Pending</option><option value="transferred_to_supplier" @selected(old('warranty_status') === 'transferred_to_supplier')>Transferred to Supplier</option><option value="received_from_supplier" @selected(old('warranty_status') === 'received_from_supplier')>Received from Supplier</option><option value="delivered_from_shop" @selected(old('warranty_status') === 'delivered_from_shop')>Delivered from Shop</option><option value="adjusted_by_supplier" @selected(old('warranty_status') === 'adjusted_by_supplier')>Adjusted by Supplier</option><option value="delivered" @selected(old('warranty_status') === 'delivered')>Delivered</option><option value="warranty_void" @selected(old('warranty_status') === 'warranty_void')>Warranty Void</option><option value="archived" @selected(old('warranty_status') === 'archived')>Archived</option></select></div>
                    <div><label for="estimated_delivery_date">Est. Delivery Date</label><input id="estimated_delivery_date" name="estimated_delivery_date" type="date" value="{{ old('estimated_delivery_date') }}"></div>
                    <div><label for="claim_status">Claim Status</label><input id="claim_status" name="claim_status" value="{{ old('claim_status') }}"></div>
                    <div><label for="product_name">Product</label><input id="product_name" name="product_name" value="{{ old('product_name') }}"></div>
                    <div><label for="sale_out_date">Sale Out Date</label><input id="sale_out_date" name="sale_out_date" type="date" value="{{ old('sale_out_date') }}"></div>
                    <div><label for="serial_no">Serial No</label><input id="serial_no" name="serial_no" value="{{ old('serial_no') }}"></div>
                    <div><label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" step=".01" min="0" value="{{ old('quantity', 1) }}"></div>
                    <div><label for="problem">Problem</label><input id="problem" name="problem" value="{{ old('problem') }}"></div>
                    <div><label for="condition">Condition</label><input id="condition" name="condition" value="{{ old('condition') }}"></div>
                    <div class="customer-address"><label for="note">Note</label><textarea id="note" name="note" rows="2">{{ old('note') }}</textarea></div>
                    <div><label>Warranty Validity</label><div class="sales-radio-group"><label><input type="radio" name="warranty_validity" value="1" @checked(old('warranty_validity', '1') == '1')> Valid</label><label><input type="radio" name="warranty_validity" value="0" @checked(old('warranty_validity') == '0')> Void</label></div></div>
                </div>
                <div class="sales-card-heading product-heading"><h2><i class="bi bi-truck"></i> Supplier Transfer</h2></div>
                <div class="sales-entry-grid customer-grid">
                    <div><label for="supplier_id">Supplier</label><select id="supplier_id" name="supplier_id"><option value="">-- Select --</option>@foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
                    <div><label for="transfer_by_employee_id">Transfer By</label><select id="transfer_by_employee_id" name="transfer_by_employee_id"><option value="">-- Select --</option>@foreach ($employees as $employee)<option value="{{ $employee->id }}" @selected(old('transfer_by_employee_id') == $employee->id)>{{ $employee->name }}</option>@endforeach</select></div>
                    <div><label for="transfer_date">Transfer Date</label><input id="transfer_date" name="transfer_date" type="date" value="{{ old('transfer_date') }}"></div>
                    <div><label for="receive_by_sr">Receive By (SR)</label><input id="receive_by_sr" name="receive_by_sr" value="{{ old('receive_by_sr') }}"></div>
                </div>
                <div class="sales-card-heading product-heading"><h2><i class="bi bi-arrow-return-left"></i> Return & Delivery</h2></div>
                <div class="sales-entry-grid customer-grid">
                    <div><label for="return_received_by_employee_id">Return Received By</label><select id="return_received_by_employee_id" name="return_received_by_employee_id"><option value="">-- Select --</option>@foreach ($employees as $employee)<option value="{{ $employee->id }}" @selected(old('return_received_by_employee_id') == $employee->id)>{{ $employee->name }}</option>@endforeach</select></div>
                    <div><label for="return_condition">Return Condition</label><input id="return_condition" name="return_condition" value="{{ old('return_condition') }}"></div>
                    <div><label for="received_date">Received Date</label><input id="received_date" name="received_date" type="date" value="{{ old('received_date') }}"></div>
                    <div><label for="delivered_by_employee_id">Delivered By</label><select id="delivered_by_employee_id" name="delivered_by_employee_id"><option value="">-- Select --</option>@foreach ($employees as $employee)<option value="{{ $employee->id }}" @selected(old('delivered_by_employee_id') == $employee->id)>{{ $employee->name }}</option>@endforeach</select></div>
                    <div><label for="delivered_date">Delivered Date</label><input id="delivered_date" name="delivered_date" type="date" value="{{ old('delivered_date') }}"></div>
                    <div class="customer-address"><label for="remarks">Remarks</label><textarea id="remarks" name="remarks" rows="2">{{ old('remarks') }}</textarea></div>
                </div>
                <div class="sales-entry-footer"><span><i class="bi bi-shield-check"></i> Warranty claim details are saved securely.</span><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-receipt"></i> Save Warranty</button></div>
            </section>
        </div>
    </form>
@stop
