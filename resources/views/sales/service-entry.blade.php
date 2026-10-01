@extends('adminlte::page')

@section('title', 'Service Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Service Module <span>›</span> Service Entry</div>
@stop

@section('content')
    <form method="POST" action="{{ route('sales.service-entry.store') }}" class="pos-sales-entry" id="service-entry-form">
        @csrf
        <div class="sales-entry-toolbar service-entry-toolbar">
            <div><span class="sales-kicker">AK COMPUTER, CCTV & LAPTOP</span><h1>Service Entry</h1><p>Register repair, installation, and maintenance services with complete customer details.</p></div>
            <div class="invoice-badge"><small>Service no</small><strong>{{ $nextInvoice }}</strong></div>
            <div class="date-field"><label for="service_date">Date</label><input id="service_date" name="service_date" type="date" value="{{ old('service_date', now()->toDateString()) }}" required></div>
        </div>
        @if (session('success')) <div class="alert alert-success sales-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
        <div class="sales-entry-layout">
            <section class="sales-entry-card sales-information-card service-information-card">
                <div class="sales-card-heading"><h2><i class="bi bi-tools"></i> Customer & Service Information</h2></div>
                <div class="sales-entry-grid customer-grid">
                    <div><label for="service_type">Service Type</label><select id="service_type" name="service_type"><option value="repair" @selected(old('service_type', 'repair') === 'repair')>Repair</option><option value="installation" @selected(old('service_type') === 'installation')>Installation</option><option value="maintenance" @selected(old('service_type') === 'maintenance')>Maintenance</option></select></div>
                    <div><label for="customer_name">Customer</label><input id="customer_name" name="customer_name" value="{{ old('customer_name', 'Cash Customer') }}" required></div>
                    <div><label for="customer_mobile">Mobile No</label><input id="customer_mobile" name="customer_mobile" value="{{ old('customer_mobile', '0') }}"></div>
                    <div><label for="technician">Technician</label><input id="technician" name="technician" value="{{ old('technician') }}" placeholder="Assign technician"></div>
                    <div class="customer-address"><label for="customer_address">Address</label><textarea id="customer_address" name="customer_address" rows="2">{{ old('customer_address') }}</textarea></div>
                </div>
                <div class="sales-card-heading product-heading"><h2><i class="bi bi-wrench-adjustable-circle"></i> Add Services</h2><button type="button" class="btn btn-sm btn-outline-primary" id="add-item"><i class="bi bi-plus-lg"></i> Add Service</button></div>
                <div class="sales-table-wrap"><table class="sales-items-table service-items-table"><thead><tr><th>Code</th><th>Service Name *</th><th>Device / Serial</th><th>Problem Description</th><th>Qty *</th><th>Rate *</th><th>Discount %</th><th>Total</th><th></th></tr></thead><tbody id="sales-items"><tr class="sale-item-row"><td><input name="items[0][service_code]" placeholder="Code"></td><td><input name="items[0][service_name]" placeholder="Repair service" required></td><td><input name="items[0][device_serial]" placeholder="Serial no"></td><td><input name="items[0][problem_description]" placeholder="Describe problem"></td><td><input name="items[0][quantity]" class="item-quantity" type="number" min=".01" step=".01" value="1" required></td><td><input name="items[0][rate]" class="item-rate" type="number" min="0" step=".01" value="0" required></td><td><input name="items[0][discount_percent]" class="item-discount" type="number" min="0" max="100" step=".01" value="0"></td><td><output class="item-total">0.00</output></td><td><button type="button" class="remove-item" aria-label="Remove service"><i class="bi bi-trash3"></i></button></td></tr></tbody></table></div>
                <div class="sales-entry-footer"><span><i class="bi bi-shield-check"></i> Customer service details are saved securely.</span><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-check2-circle"></i> Save Service</button></div>
            </section>
            <aside class="sales-entry-card amount-card"><div class="sales-card-heading"><h2><i class="bi bi-calculator"></i> Amount Details</h2></div><div class="amount-fields"><label>Sub Total <output id="subtotal">0.00</output></label><label>VAT <input name="vat" id="vat" type="number" min="0" step=".01" value="{{ old('vat', 0) }}"></label><label>Discount <input name="discount" id="discount" type="number" min="0" step=".01" value="{{ old('discount', 0) }}"></label><label>Transport Cost <input name="transport_cost" id="transport_cost" type="number" min="0" step=".01" value="{{ old('transport_cost', 0) }}"></label><label class="total-field">Total <output id="total">0.00</output></label><label>Paid <input name="paid" id="paid" type="number" min="0" step=".01" value="{{ old('paid', 0) }}"></label><label class="due-field">Due <output id="due">0.00</output></label></div><div class="amount-note"><i class="bi bi-info-circle"></i> Due amount is calculated automatically.</div></aside>
        </div>
    </form>
@stop

@push('css')
<style>.service-information-card { background: linear-gradient(180deg, #f0ebff 0, #e6ddff 255px, #fff 255px); }.service-information-card .sales-card-heading { border-color: #d5c6f2; }.service-entry-toolbar { background: linear-gradient(115deg, #34216c, #6b3db5); }.service-items-table { min-width: 1040px; }.sales-entry-grid select { width: 100%; padding: .42rem .55rem; border: 1px solid #cad8e5; border-radius: 4px; background: #fff; color: #1e3655; font-size: .8rem; }</style>
@endpush

@push('js')
<script>
(() => {
    const tbody = document.querySelector('#sales-items'); let index = 1;
    const money = value => Number(value || 0).toFixed(2);
    const recalculate = () => {
        let subtotal = 0;
        tbody.querySelectorAll('.sale-item-row').forEach(row => { const rowSubtotal = (Number(row.querySelector('.item-quantity').value) || 0) * (Number(row.querySelector('.item-rate').value) || 0); const discount = rowSubtotal * ((Number(row.querySelector('.item-discount').value) || 0) / 100); row.querySelector('.item-total').textContent = money(rowSubtotal - discount); subtotal += rowSubtotal; });
        const total = Math.max(0, subtotal + Number(document.querySelector('#vat').value || 0) + Number(document.querySelector('#transport_cost').value || 0) - Number(document.querySelector('#discount').value || 0)); document.querySelector('#subtotal').textContent = money(subtotal); document.querySelector('#total').textContent = money(total); document.querySelector('#due').textContent = money(Math.max(0, total - Number(document.querySelector('#paid').value || 0)));
    };
    document.querySelector('#add-item').addEventListener('click', () => { const row = tbody.querySelector('.sale-item-row').cloneNode(true); row.querySelectorAll('input').forEach(input => { input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`); input.value = input.classList.contains('item-quantity') ? '1' : input.classList.contains('item-discount') ? '0' : ''; }); row.querySelector('.item-total').textContent = '0.00'; tbody.appendChild(row); index++; recalculate(); });
    tbody.addEventListener('click', event => { const button = event.target.closest('.remove-item'); if (button && tbody.children.length > 1) { button.closest('tr').remove(); recalculate(); } }); document.querySelector('#service-entry-form').addEventListener('input', recalculate); recalculate();
})();
</script>
@endpush
