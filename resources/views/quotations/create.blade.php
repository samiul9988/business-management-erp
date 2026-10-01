@extends('adminlte::page')

@section('title', 'Quotation Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> Quotation Entry</div>
@stop

@section('content')
    <form method="POST" action="{{ route('quotations.entry.store') }}" class="pos-sales-entry" id="quotation-entry-form">
        @csrf
        <div class="sales-entry-toolbar">
            <div><span class="sales-kicker">AK COMPUTER, CCTV & LAPTOP</span><h1>Quotation Entry</h1><p>Prepare and save a quotation for a customer.</p></div>
            <div class="invoice-badge"><small>Quotation no</small><strong>{{ $nextQuotationNo }}</strong></div>
            <div class="date-field"><label for="quotation_date">Date</label><input id="quotation_date" name="quotation_date" type="date" value="{{ old('quotation_date', now()->toDateString()) }}" required></div>
            <div class="date-field"><label for="valid_until">Valid Until</label><input id="valid_until" name="valid_until" type="date" value="{{ old('valid_until', now()->addDays(7)->toDateString()) }}"></div>
        </div>
        @if (session('success')) <div class="alert alert-success sales-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
        <div class="sales-entry-layout">
            <section class="sales-entry-card sales-information-card">
                <div class="sales-card-heading"><h2><i class="bi bi-person-vcard"></i> Customer & Product Information</h2></div>
                <div class="sales-entry-grid customer-grid">
                    <div><label for="customer_name">Customer</label><input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required></div>
                    <div><label for="customer_mobile">Mobile No</label><input id="customer_mobile" name="customer_mobile" value="{{ old('customer_mobile') }}"></div>
                    <div class="customer-address"><label for="customer_address">Address</label><textarea id="customer_address" name="customer_address" rows="2">{{ old('customer_address') }}</textarea></div>
                </div>
                <div class="sales-card-heading product-heading"><h2><i class="bi bi-box-seam"></i> Add Products</h2><button type="button" class="btn btn-sm btn-outline-primary" id="add-item"><i class="bi bi-plus-lg"></i> Add Product</button></div>
                <div class="sales-table-wrap"><table class="sales-items-table"><thead><tr><th>Code</th><th>Product Name *</th><th>Qty *</th><th>Rate *</th><th>Discount %</th><th>Total</th><th></th></tr></thead><tbody id="quotation-items"><tr class="sale-item-row"><td><input name="items[0][product_code]" placeholder="SKU"></td><td><input name="items[0][product_name]" placeholder="Enter product" required></td><td><input name="items[0][quantity]" class="item-quantity" type="number" min=".01" step=".01" value="1" required></td><td><input name="items[0][rate]" class="item-rate" type="number" min="0" step=".01" value="0" required></td><td><input name="items[0][discount_percent]" class="item-discount" type="number" min="0" max="100" step=".01" value="0"></td><td><output class="item-total">0.00</output></td><td><button type="button" class="remove-item" aria-label="Remove item"><i class="bi bi-trash3"></i></button></td></tr></tbody></table></div>
                <div class="sales-entry-footer"><span><i class="bi bi-shield-check"></i> Quotations do not affect stock until converted to a sale.</span><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-receipt"></i> Save Quotation</button></div>
            </section>
            <aside class="sales-entry-card amount-card"><div class="sales-card-heading"><h2><i class="bi bi-calculator"></i> Amount Details</h2></div><div class="amount-fields"><label>Sub Total <output id="subtotal">0.00</output></label><label>VAT <input name="vat" id="vat" type="number" min="0" step=".01" value="{{ old('vat', 0) }}"></label><label>Discount <input name="discount" id="discount" type="number" min="0" step=".01" value="{{ old('discount', 0) }}"></label><label>Transport Cost <input name="transport_cost" id="transport_cost" type="number" min="0" step=".01" value="{{ old('transport_cost', 0) }}"></label><label class="total-field">Total <output id="total">0.00</output></label></div></aside>
        </div>
    </form>
@stop

@push('js')
<script>
(() => {
    const tbody = document.querySelector('#quotation-items');
    let index = 1;
    const money = value => Number(value || 0).toFixed(2);
    const recalculate = () => {
        let subtotal = 0;
        tbody.querySelectorAll('.sale-item-row').forEach(row => {
            const rowSubtotal = (Number(row.querySelector('.item-quantity').value) || 0) * (Number(row.querySelector('.item-rate').value) || 0);
            const discount = rowSubtotal * ((Number(row.querySelector('.item-discount').value) || 0) / 100);
            row.querySelector('.item-total').value = money(rowSubtotal - discount);
            row.querySelector('.item-total').textContent = money(rowSubtotal - discount);
            subtotal += rowSubtotal;
        });
        const total = Math.max(0, subtotal + Number(document.querySelector('#vat').value || 0) + Number(document.querySelector('#transport_cost').value || 0) - Number(document.querySelector('#discount').value || 0));
        document.querySelector('#subtotal').textContent = money(subtotal);
        document.querySelector('#total').textContent = money(total);
    };
    document.querySelector('#add-item').addEventListener('click', () => {
        const row = tbody.querySelector('.sale-item-row').cloneNode(true);
        row.querySelectorAll('input').forEach(input => { input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`); input.value = input.classList.contains('item-quantity') ? '1' : input.classList.contains('item-discount') ? '0' : ''; });
        row.querySelector('.item-total').textContent = '0.00'; tbody.appendChild(row); index++; recalculate();
    });
    tbody.addEventListener('click', event => { const button = event.target.closest('.remove-item'); if (button && tbody.children.length > 1) { button.closest('tr').remove(); recalculate(); } });
    document.querySelector('#quotation-entry-form').addEventListener('input', recalculate); recalculate();
})();
</script>
@endpush
