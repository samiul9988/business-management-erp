@extends('adminlte::page')

@section('title', 'Repair Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Repair Module <span>›</span> Repair Entry</div>
@stop

@section('content')
    <form method="POST" action="{{ route('repair.entry.store') }}" class="pos-sales-entry" id="repair-entry-form">
        @csrf
        <div class="sales-entry-toolbar">
            <div><span class="sales-kicker">AK COMPUTER, CCTV & LAPTOP</span><h1>Repair Entry</h1><p>Log a device repair job with parts and billing details.</p></div>
            <div class="invoice-badge"><small>Invoice no</small><strong>{{ $nextInvoice }}</strong></div>
            <div class="date-field"><label for="repair_date">Date</label><input id="repair_date" name="repair_date" type="date" value="{{ old('repair_date', now()->toDateString()) }}" required></div>
        </div>
        @if (session('success')) <div class="alert alert-success sales-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
        <div class="sales-entry-layout">
            <section class="sales-entry-card sales-information-card">
                <div class="sales-card-heading"><h2><i class="bi bi-person-vcard"></i> Customer & Device Information</h2></div>
                <div class="sales-entry-grid customer-grid">
                    <div><label for="customer_name">Customer Name</label><input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required></div>
                    <div><label for="customer_mobile">Mobile No</label><input id="customer_mobile" name="customer_mobile" value="{{ old('customer_mobile') }}"></div>
                    <div><label for="assigned_employee_id">Assign To</label><select id="assigned_employee_id" name="assigned_employee_id"><option value="">-- Select --</option>@foreach ($employees as $employee)<option value="{{ $employee->id }}" @selected(old('assigned_employee_id') == $employee->id)>{{ $employee->name }}</option>@endforeach</select></div>
                    <div><label for="expected_delivery_date">Exp. Delivery Date</label><input id="expected_delivery_date" name="expected_delivery_date" type="date" value="{{ old('expected_delivery_date') }}"></div>
                    <div class="customer-address"><label for="customer_address">Address</label><textarea id="customer_address" name="customer_address" rows="2">{{ old('customer_address') }}</textarea></div>
                    <div><label for="status">Rep. Status</label><select id="status" name="status" required><option value="pending" @selected(old('status', 'pending') === 'pending')>Pending</option><option value="completed" @selected(old('status') === 'completed')>Completed</option><option value="non_completed" @selected(old('status') === 'non_completed')>Non-Completed</option><option value="delivered" @selected(old('status') === 'delivered')>Delivered</option><option value="transfer" @selected(old('status') === 'transfer')>Transfer</option><option value="received" @selected(old('status') === 'received')>Received</option></select></div>
                    <div><label for="repair_company_id">Company</label><select id="repair_company_id" name="repair_company_id"><option value="">-- Select --</option>@foreach ($repairCompanies as $company)<option value="{{ $company->id }}" @selected(old('repair_company_id') == $company->id)>{{ $company->name }}</option>@endforeach</select></div>
                    <div><label for="brand">Brand</label><input id="brand" name="brand" value="{{ old('brand') }}"></div>
                    <div><label for="model">Model</label><input id="model" name="model" value="{{ old('model') }}"></div>
                    <div><label for="serial_no">Serial No</label><input id="serial_no" name="serial_no" value="{{ old('serial_no') }}"></div>
                    <div class="customer-address"><label for="problem">Problem</label><textarea id="problem" name="problem" rows="2">{{ old('problem') }}</textarea></div>
                    <div><label>Warranty Period</label><div class="sales-radio-group"><label><input type="radio" name="warranty_period" value="1" @checked(old('warranty_period') == '1')> Yes</label><label><input type="radio" name="warranty_period" value="0" @checked(old('warranty_period', '0') == '0')> No</label></div></div>
                    <div><label style="display:flex; align-items:center; gap:.4rem;"><input type="checkbox" name="extra_received" value="1" style="width:auto;" @checked(old('extra_received'))> Extra Received</label></div>
                </div>
                <div class="sales-card-heading product-heading"><h2><i class="bi bi-tools"></i> Parts Information</h2></div>
                <div class="sales-entry-grid customer-grid">
                    <div><label for="parts_name">Parts Name</label><input id="parts_name" name="parts_name" value="{{ old('parts_name') }}"></div>
                    <div><label for="parts_supplier_id">Supplier</label><select id="parts_supplier_id" name="parts_supplier_id"><option value="">-- Select --</option>@foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('parts_supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
                    <div><label for="parts_purchase_date">Pur. Date</label><input id="parts_purchase_date" name="parts_purchase_date" type="date" value="{{ old('parts_purchase_date') }}"></div>
                    <div><label for="parts_purchase_rate">Pur. Rate</label><input id="parts_purchase_rate" name="parts_purchase_rate" type="number" step=".01" min="0" value="{{ old('parts_purchase_rate', 0) }}"></div>
                    <div><label for="parts_quantity">Quantity</label><input id="parts_quantity" name="parts_quantity" type="number" step=".01" min="0" value="{{ old('parts_quantity', 0) }}"></div>
                    <div><label for="parts_warranty_days">WTY (Days)</label><input id="parts_warranty_days" name="parts_warranty_days" type="number" min="0" value="{{ old('parts_warranty_days', 0) }}"></div>
                </div>
                <div class="sales-entry-footer"><span><i class="bi bi-shield-check"></i> Repair job and billing details are saved securely.</span><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-receipt"></i> Save Repair</button></div>
            </section>
            <aside class="sales-entry-card amount-card"><div class="sales-card-heading"><h2><i class="bi bi-calculator"></i> Amount Details</h2></div><div class="amount-fields"><label>Sub Total <input name="subtotal" id="subtotal" type="number" min="0" step=".01" value="{{ old('subtotal', 0) }}"></label><label>Discount <input name="discount" id="discount" type="number" min="0" step=".01" value="{{ old('discount', 0) }}"></label><label class="total-field">Total <output id="total">0.00</output></label><label>Payment Method</label><div class="sales-radio-group"><label><input type="radio" name="payment_method" value="cash" @checked(old('payment_method', 'cash') === 'cash')> Cash</label><label><input type="radio" name="payment_method" value="mfs" @checked(old('payment_method') === 'mfs')> MFS</label></div><label>Paid <input name="paid" id="paid" type="number" min="0" step=".01" value="{{ old('paid', 0) }}"></label><label class="due-field">Due <output id="due">0.00</output></label></div></aside>
        </div>
    </form>
@stop

@push('js')
<script>
(() => {
    const money = value => Number(value || 0).toFixed(2);
    const recalculate = () => {
        const total = Math.max(0, Number(document.querySelector('#subtotal').value || 0) - Number(document.querySelector('#discount').value || 0));
        document.querySelector('#total').textContent = money(total);
        document.querySelector('#due').textContent = money(Math.max(0, total - Number(document.querySelector('#paid').value || 0)));
    };
    document.querySelector('#repair-entry-form').addEventListener('input', recalculate);
    recalculate();
})();
</script>
@endpush
