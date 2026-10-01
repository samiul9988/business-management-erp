@extends('adminlte::page')

@section('title', 'Purchase Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Purchase Module <span>›</span> Purchase Entry</div>
@stop

@section('content')
    <form method="POST" action="{{ route('purchase.entry.store') }}" class="pos-sales-entry" id="purchase-entry-form">
        @csrf
        <div class="sales-entry-toolbar">
            <div><span class="sales-kicker">AK COMPUTER, CCTV & LAPTOP</span><h1>Purchase Entry</h1><p>Record a supplier purchase invoice in seconds.</p></div>
            <div class="invoice-badge"><small>Invoice no</small><strong>{{ $nextInvoice }}</strong></div>
            <div class="date-field"><label for="purchase_date">Date</label><input id="purchase_date" name="purchase_date" type="date" value="{{ old('purchase_date', now()->toDateString()) }}" required></div>
        </div>
        @if (session('success')) <div class="alert alert-success sales-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
        <div class="sales-entry-layout">
            <section class="sales-entry-card sales-information-card">
                <div class="sales-card-heading"><h2><i class="bi bi-truck"></i> Supplier & Product Information</h2></div>
                <div class="sales-entry-grid customer-grid">
                    <div><label for="supplier_id">Supplier</label><select id="supplier_id" name="supplier_id"><option value="">-- Select --</option>@foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}" data-mobile="{{ $supplier->mobile }}" data-address="{{ $supplier->address }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
                    <div><label for="supplier_name">Supplier Name</label><input id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required></div>
                    <div><label for="supplier_mobile">Mobile No</label><input id="supplier_mobile" name="supplier_mobile" value="{{ old('supplier_mobile') }}"></div>
                    <div class="customer-address"><label for="supplier_address">Address</label><textarea id="supplier_address" name="supplier_address" rows="2">{{ old('supplier_address') }}</textarea></div>
                </div>
                <div class="sales-card-heading product-heading"><h2><i class="bi bi-box-seam"></i> Add Products</h2><button type="button" class="btn btn-sm btn-outline-primary" id="add-item"><i class="bi bi-plus-lg"></i> Add Product</button></div>
                <div class="sales-table-wrap"><table class="sales-items-table"><thead><tr><th>Code</th><th>Product Name *</th><th>Warranty (Days)</th><th>Qty *</th><th>Rate *</th><th>Total</th><th></th></tr></thead><tbody id="purchase-items"><tr class="sale-item-row"><td><input name="items[0][product_code]" placeholder="SKU"></td><td><input name="items[0][product_name]" placeholder="Product name" required></td><td><input name="items[0][warranty_days]" type="number" min="0" placeholder="0"></td><td><input name="items[0][quantity]" class="item-quantity" type="number" min=".01" step=".01" value="1" required></td><td><input name="items[0][rate]" class="item-rate" type="number" min="0" step=".01" value="0" required></td><td><output class="item-total">0.00</output></td><td><button type="button" class="remove-item" aria-label="Remove item"><i class="bi bi-trash3"></i></button></td></tr></tbody></table></div>
                <div class="sales-entry-footer"><span><i class="bi bi-shield-check"></i> Stock and invoice details are saved securely.</span><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-receipt"></i> Save Purchase</button></div>
            </section>
            <aside class="sales-entry-card amount-card"><div class="sales-card-heading"><h2><i class="bi bi-calculator"></i> Amount Details</h2></div><div class="amount-fields"><label>Sub Total <output id="subtotal">0.00</output></label><label>VAT <input name="vat" id="vat" type="number" min="0" step=".01" value="{{ old('vat', 0) }}"></label><label>Discount <input name="discount" id="discount" type="number" min="0" step=".01" value="{{ old('discount', 0) }}"></label><label>Transport / Labour Cost <input name="transport_cost" id="transport_cost" type="number" min="0" step=".01" value="{{ old('transport_cost', 0) }}"></label><label class="total-field">Total <output id="total">0.00</output></label><label>Cash Paid <input name="paid" id="paid" type="number" min="0" step=".01" value="{{ old('paid', 0) }}"></label><label class="due-field">Due <output id="due">0.00</output></label></div><div class="amount-note"><i class="bi bi-info-circle"></i> Due amount is calculated automatically.</div></aside>
        </div>
    </form>
@stop

@push('js')
<script>
(() => {
    const tbody = document.querySelector('#purchase-items');
    let index = 1;
    const money = value => Number(value || 0).toFixed(2);
    const recalculate = () => {
        let subtotal = 0;
        tbody.querySelectorAll('.sale-item-row').forEach(row => {
            const rowTotal = (Number(row.querySelector('.item-quantity').value) || 0) * (Number(row.querySelector('.item-rate').value) || 0);
            row.querySelector('.item-total').textContent = money(rowTotal);
            subtotal += rowTotal;
        });
        const total = Math.max(0, subtotal + Number(document.querySelector('#vat').value || 0) + Number(document.querySelector('#transport_cost').value || 0) - Number(document.querySelector('#discount').value || 0));
        document.querySelector('#subtotal').textContent = money(subtotal);
        document.querySelector('#total').textContent = money(total);
        document.querySelector('#due').textContent = money(Math.max(0, total - Number(document.querySelector('#paid').value || 0)));
    };
    document.querySelector('#add-item').addEventListener('click', () => {
        const row = tbody.querySelector('.sale-item-row').cloneNode(true);
        row.querySelectorAll('input').forEach(input => { input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`); input.value = input.classList.contains('item-quantity') ? '1' : ''; });
        row.querySelector('.item-total').textContent = '0.00'; tbody.appendChild(row); index++; recalculate();
    });
    tbody.addEventListener('click', event => { const button = event.target.closest('.remove-item'); if (button && tbody.children.length > 1) { button.closest('tr').remove(); recalculate(); } });
    document.querySelector('#supplier_id').addEventListener('change', event => {
        const option = event.target.selectedOptions[0];
        if (!option || !option.value) return;
        document.querySelector('#supplier_name').value = option.dataset.name || '';
        document.querySelector('#supplier_mobile').value = option.dataset.mobile || '';
        document.querySelector('#supplier_address').value = option.dataset.address || '';
    });
    document.querySelector('#purchase-entry-form').addEventListener('input', recalculate);
    recalculate();
})();
</script>
@endpush
