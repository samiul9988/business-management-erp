@extends('adminlte::page')

@section('title', 'Sales Return')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Return</div>
@stop

@section('content')
    <form method="POST" action="{{ route('sales.return.store') }}" class="pos-sales-entry sales-return-page" id="sales-return-form">
        @csrf
        <div class="sales-entry-toolbar return-entry-toolbar">
            <div><span class="sales-kicker">3G COMPUTERS</span><h1>Sales Return</h1><p>Return sold products safely and update the original invoice balance.</p></div>
            <div class="invoice-badge"><small>Return no</small><strong>{{ $nextReturn }}</strong></div>
            <div class="date-field"><label for="return_date">Date</label><input id="return_date" name="return_date" type="date" value="{{ old('return_date', now()->toDateString()) }}" required></div>
        </div>
        @if (session('success')) <div class="alert alert-success sales-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
        <section class="return-selector-card">
            <div class="return-selector-field"><label for="invoice_no"><i class="bi bi-receipt"></i> Invoice</label><select name="invoice_no" id="invoice_no" required><option value="">Select Invoice</option>@foreach ($sales as $sale)<option value="{{ $sale->invoice_no }}" @selected(old('invoice_no') === $sale->invoice_no)>{{ $sale->invoice_no }} — {{ $sale->customer_name }} — {{ number_format((float) $sale->total, 2) }}</option>@endforeach</select></div>
            <div class="return-selector-field"><label for="customer_name"><i class="bi bi-person"></i> Customer</label><input id="customer_name" name="customer_name" value="{{ old('customer_name', 'Cash Customer') }}" required></div>
            <div class="return-selector-field return-reason-field"><label for="reason"><i class="bi bi-chat-left-text"></i> Return Reason</label><input id="reason" name="reason" value="{{ old('reason') }}" placeholder="Reason for return"></div>
        </section>
        <div class="return-entry-layout">
            <section class="sales-entry-card sales-information-card return-information-card"><div class="sales-card-heading"><h2><i class="bi bi-arrow-counterclockwise"></i> Return Items</h2><button type="button" class="btn btn-sm btn-outline-primary" id="add-item"><i class="bi bi-plus-lg"></i> Add Item</button></div><div class="sales-table-wrap"><table class="sales-items-table return-items-table"><thead><tr><th>Code</th><th>Product Name *</th><th>Serial No</th><th>Qty *</th><th>Rate *</th><th>Return Reason</th><th>Total</th><th></th></tr></thead><tbody id="return-items"><tr class="sale-item-row"><td><input name="items[0][product_code]" placeholder="Code"></td><td><input name="items[0][product_name]" placeholder="Returned product" required></td><td><input name="items[0][serial_no]" placeholder="Serial no"></td><td><input name="items[0][quantity]" class="item-quantity" type="number" min=".01" step=".01" value="1" required></td><td><input name="items[0][rate]" class="item-rate" type="number" min="0" step=".01" value="0" required></td><td><input name="items[0][reason]" placeholder="Damaged / wrong item"></td><td><output class="item-total">0.00</output></td><td><button type="button" class="remove-item" aria-label="Remove item"><i class="bi bi-trash3"></i></button></td></tr></tbody></table></div><div class="sales-entry-footer"><span><i class="bi bi-info-circle"></i> The original invoice balance will be adjusted after saving.</span><button class="btn btn-danger btn-lg" type="submit"><i class="bi bi-arrow-return-left"></i> Save Return</button></div></section>
            <aside class="sales-entry-card amount-card return-amount-card"><div class="sales-card-heading"><h2><i class="bi bi-calculator"></i> Return Details</h2></div><div class="amount-fields"><label>Original Total <output id="original-total">0.00</output></label><label>Return Sub Total <output id="subtotal">0.00</output></label><label class="total-field">Refund / Adjustment <output id="total">0.00</output></label></div><div class="amount-note"><i class="bi bi-shield-check"></i> Return amount cannot exceed the selected invoice.</div></aside>
        </div>
    </form>
@stop


@push('js')
<script>
(() => {
    const sales = @json($salesData);
    const invoice = document.querySelector('#invoice_no'); const customer = document.querySelector('#customer_name'); const original = document.querySelector('#original-total'); const tbody = document.querySelector('#return-items'); let index = 1;
    const money = value => Number(value || 0).toFixed(2);
    const recalculate = () => { let total = 0; tbody.querySelectorAll('.sale-item-row').forEach(row => { const line = (Number(row.querySelector('.item-quantity').value) || 0) * (Number(row.querySelector('.item-rate').value) || 0); row.querySelector('.item-total').textContent = money(line); total += line; }); document.querySelector('#subtotal').textContent = money(total); document.querySelector('#total').textContent = money(total); };
    invoice.addEventListener('change', () => { const selected = sales[invoice.value]; if (!selected) { original.textContent = '0.00'; return; } customer.value = selected.customer; original.textContent = money(selected.total); const first = selected.items[0]; if (first) { const row = tbody.querySelector('.sale-item-row'); row.querySelector('[name$="[product_code]"]').value = first.code || ''; row.querySelector('[name$="[product_name]"]').value = first.name; row.querySelector('.item-rate').value = first.rate; recalculate(); } });
    document.querySelector('#add-item').addEventListener('click', () => { const row = tbody.querySelector('.sale-item-row').cloneNode(true); row.querySelectorAll('input').forEach(input => { input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`); input.value = input.classList.contains('item-quantity') ? '1' : ''; }); row.querySelector('.item-total').textContent = '0.00'; tbody.appendChild(row); index++; });
    tbody.addEventListener('click', event => { const button = event.target.closest('.remove-item'); if (button && tbody.children.length > 1) { button.closest('tr').remove(); recalculate(); } }); document.querySelector('#sales-return-form').addEventListener('input', recalculate); recalculate();
})();
</script>
@endpush
