@extends('adminlte::page')

@section('title', $sale ? 'Edit Sale' : 'Sales Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Product Sales <span>›</span> {{ $sale ? 'Edit Sale' : 'Sales Entry' }}</div>
@stop

@section('content')
    <form method="POST" action="{{ $sale ? route('sales.record.update', $sale) : route('sales.entry.store') }}" class="pos-sales-entry" id="sales-entry-form">
        @csrf
        @if ($sale) @method('PUT') @endif
        <div class="sales-entry-toolbar">
            <div><span class="sales-kicker">3G COMPUTERS</span><h1>{{ $sale ? 'Edit Sale' : 'Sales Entry' }}</h1><p>{{ $sale ? 'Update the products, customer, or amounts on this invoice.' : 'Create a complete retail or wholesale invoice in seconds.' }}</p></div>
            <div class="invoice-badge"><small>Invoice no</small><strong>{{ $nextInvoice }}</strong></div>
            <div class="date-field"><label for="sale_date">Date</label><input id="sale_date" name="sale_date" type="date" value="{{ old('sale_date', $sale?->sale_date->toDateString() ?? now()->toDateString()) }}" required></div>
        </div>
        @if (session('success')) <div class="alert alert-success sales-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
        @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
        <div class="sales-entry-layout">
            <section class="sales-entry-card sales-information-card">
                <div class="sales-card-heading"><h2><i class="bi bi-person-vcard"></i> Customer & Product Information</h2></div>
                <div class="sales-entry-grid customer-grid">
                    <div><label>Sales Type</label><div class="sales-radio-group"><label><input type="radio" name="sale_type" value="retail" @checked(old('sale_type', $sale?->sale_type ?? 'retail') === 'retail')> Retail</label><label><input type="radio" name="sale_type" value="wholesale" @checked(old('sale_type', $sale?->sale_type) === 'wholesale')> Wholesale</label></div></div>

                    <div class="combo-field">
                        <label for="customer_search">Customer</label>
                        <div class="combo-input-group">
                            <input type="text" id="customer_search" autocomplete="off" placeholder="C00001 - Cash Customer" value="{{ $sale?->customer ? $sale->customer->customer_code.' - '.$sale->customer->name : '' }}">
                            <a href="{{ route('customer.index') }}" target="_blank" class="combo-add-btn" title="Add new customer"><i class="bi bi-plus-lg"></i></a>
                        </div>
                        <ul class="combo-dropdown" id="customer-dropdown"></ul>
                    </div>

                    <div><label for="customer_name">Name</label><input id="customer_name" name="customer_name" value="{{ old('customer_name', $sale->customer_name ?? 'Cash Customer') }}" required></div>
                    <div><label for="customer_mobile">Mobile No</label><input id="customer_mobile" name="customer_mobile" value="{{ old('customer_mobile', $sale->customer_mobile ?? '0') }}"></div>
                    <div class="customer-address"><label for="customer_address">Address</label><textarea id="customer_address" name="customer_address" rows="2">{{ old('customer_address', $sale->customer_address ?? '') }}</textarea></div>
                </div>

                <div class="sales-card-heading product-heading"><h2><i class="bi bi-box-seam"></i> Add Products</h2></div>
                <div class="product-staging-grid">
                    <div class="combo-field product-combo">
                        <label>Product</label>
                        <div class="combo-input-group">
                            <input type="text" id="product_search" autocomplete="off" placeholder="Select Product" value="{{ $searchedBarcode }}">
                            <a href="{{ route('product.index') }}" target="_blank" class="combo-add-btn" title="Add new product"><i class="bi bi-plus-lg"></i></a>
                        </div>
                        <ul class="combo-dropdown" id="product-dropdown"></ul>
                    </div>
                    <div><label>Sale Rate</label><input type="number" id="stage_rate" step=".01" min="0" value="0"></div>
                    <div><label>Qty</label><input type="number" id="stage_qty" step=".01" min=".01" value="1"></div>
                    <div><label>Amount</label><output id="stage_amount">0.00</output></div>
                    <div><label>Warranty (Days)</label><input type="number" id="stage_warranty" min="0" value="0"></div>
                    <div><label>Discount %</label><input type="number" id="stage_discount" min="0" max="100" step=".01" value="0"></div>
                    <div class="mfs-check"><label><input type="checkbox" id="stage_by_serial" disabled> By Serial</label></div>
                    <div class="stage-serial-field" id="stage_serial_wrap"><label>Serial</label><select id="stage_serial" disabled><option value="">Select Serial</option></select></div>
                    <div class="stage-add-cell"><button type="button" class="btn btn-primary" id="add-to-cart-btn"><i class="bi bi-cart-plus"></i> Add to Cart</button></div>
                </div>

                <div class="sales-table-wrap">
                    <table class="sales-items-table">
                        <thead><tr><th>Sl</th><th>Code</th><th>Product Name</th><th>Wrnty (Days)</th><th>Qty</th><th>Rate</th><th>Subtotal</th><th>Dis. %</th><th>Dis. ৳</th><th>Total</th><th></th></tr></thead>
                        <tbody id="cart-items"></tbody>
                    </table>
                    <p class="empty-cart-note" id="empty-cart-note">No products added yet. Search a product above and click "Add to Cart".</p>
                </div>

                <div class="sales-entry-footer"><span><i class="bi bi-shield-check"></i> Stock and invoice details are saved securely.</span><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-receipt"></i> {{ $sale ? 'Update Sale' : 'Save Sale' }}</button></div>
            </section>

            <aside class="sales-entry-card amount-card">
                <div class="sales-card-heading"><h2><i class="bi bi-calculator"></i> Amount Details</h2></div>
                <div class="amount-fields">
                    <label>Sub Total <output id="subtotal">0.00</output></label>

                    <label class="amount-pair-label">VAT
                        <div class="amount-pair">
                            <input type="number" id="vat_percent" min="0" step=".01" value="0" placeholder="%">
                            <input type="number" id="vat" name="vat" min="0" step=".01" value="{{ old('vat', $sale->vat ?? 0) }}" placeholder="৳">
                        </div>
                    </label>

                    <label class="amount-pair-label">Discount
                        <div class="amount-pair">
                            <input type="number" id="discount_percent" min="0" step=".01" value="0" placeholder="%">
                            <input type="number" id="discount" name="discount" min="0" step=".01" value="{{ old('discount', $sale ? max(0, $sale->discount - $sale->items->sum('discount_amount')) : 0) }}" placeholder="৳">
                        </div>
                    </label>

                    <label>Transport Cost <input name="transport_cost" id="transport_cost" type="number" min="0" step=".01" value="{{ old('transport_cost', $sale->transport_cost ?? 0) }}"></label>
                    <label class="total-field">Total <output id="total">0.00</output></label>

                    <label class="mfs-check"><input type="checkbox" id="mfs_toggle"> Mobile Financial System</label>

                    <label id="paid_label">Cash <input name="paid" id="paid" type="number" min="0" step=".01" value="{{ old('paid', $sale->paid ?? 0) }}"></label>
                    <label class="due-field">Due <output id="due">0.00</output></label>
                </div>
                <div class="amount-note"><i class="bi bi-info-circle"></i> Due amount is calculated automatically.</div>
            </aside>
        </div>
    </form>
@stop

@push('js')
<script>
(() => {
    const customers = @json($customers);
    const products = @json($products);
    let serialsByProduct = @json($serialsByProduct);

    const money = value => Number(value || 0).toFixed(2);

    /* ---------- generic searchable combobox ---------- */
    function setupCombo(inputEl, dropdownEl, items, formatLabel, filterFn, onSelect) {
        const render = (list) => {
            dropdownEl.innerHTML = '';
            if (! list.length) { dropdownEl.classList.remove('show'); return; }
            list.slice(0, 50).forEach(item => {
                const li = document.createElement('li');
                li.textContent = formatLabel(item);
                li.addEventListener('mousedown', (e) => { e.preventDefault(); inputEl.value = formatLabel(item); dropdownEl.classList.remove('show'); onSelect(item); });
                dropdownEl.appendChild(li);
            });
            dropdownEl.classList.add('show');
        };
        inputEl.addEventListener('input', () => render(filterFn(inputEl.value)));
        inputEl.addEventListener('focus', () => render(filterFn(inputEl.value)));
        inputEl.addEventListener('blur', () => setTimeout(() => dropdownEl.classList.remove('show'), 150));
    }

    const customerLabel = c => `${c.code} - ${c.name}${c.mobile ? ' - '+c.mobile : ''}`;
    setupCombo(
        document.querySelector('#customer_search'),
        document.querySelector('#customer-dropdown'),
        customers,
        customerLabel,
        (q) => { const needle = q.toLowerCase(); return customers.filter(c => customerLabel(c).toLowerCase().includes(needle)); },
        (c) => {
            document.querySelector('#customer_name').value = c.name;
            document.querySelector('#customer_mobile').value = c.mobile || '0';
            document.querySelector('#customer_address').value = c.address || '';
        }
    );

    const productLabel = p => `${p.name} - ${p.color} - ${p.code}`;
    const saleType = () => document.querySelector('input[name="sale_type"]:checked')?.value || 'retail';
    setupCombo(
        document.querySelector('#product_search'),
        document.querySelector('#product-dropdown'),
        products,
        productLabel,
        (q) => { const needle = q.toLowerCase(); return products.filter(p => productLabel(p).toLowerCase().includes(needle)); },
        (p) => {
            stagedProduct = p;
            document.querySelector('#stage_rate').value = saleType() === 'wholesale' ? p.wholesale_rate : p.rate;
            document.querySelector('#stage_warranty').value = p.warranty_days || 0;
            const toggle = document.querySelector('#stage_by_serial');
            toggle.checked = false;
            toggle.disabled = ! (serialsByProduct[p.code] || []).length;
            syncSerialStage();
            recalcStage();
        }
    );

    /* ---------- staging area ---------- */
    let stagedProduct = null;
    const recalcStage = () => {
        const rate = Number(document.querySelector('#stage_rate').value) || 0;
        const qty = Number(document.querySelector('#stage_qty').value) || 0;
        document.querySelector('#stage_amount').textContent = money(rate * qty);
    };
    ['#stage_rate', '#stage_qty'].forEach(sel => document.querySelector(sel).addEventListener('input', recalcStage));

    /* ---------- by-serial staging ---------- */
    const takeSerial = (code, serial) => { if (serialsByProduct[code]) { serialsByProduct[code] = serialsByProduct[code].filter(s => s !== serial); } };
    const releaseSerial = (code, serial) => {
        if (! serial) return;
        if (! serialsByProduct[code]) serialsByProduct[code] = [];
        if (! serialsByProduct[code].includes(serial)) serialsByProduct[code].push(serial);
    };
    const populateSerialSelect = (code) => {
        const select = document.querySelector('#stage_serial');
        select.innerHTML = '<option value="">Select Serial</option>';
        (serialsByProduct[code] || []).forEach(s => {
            const opt = document.createElement('option');
            opt.value = s; opt.textContent = s;
            select.appendChild(opt);
        });
    };
    const syncSerialStage = () => {
        const toggle = document.querySelector('#stage_by_serial');
        const wrap = document.querySelector('#stage_serial_wrap');
        const select = document.querySelector('#stage_serial');
        const qty = document.querySelector('#stage_qty');
        if (toggle.checked) {
            wrap.classList.add('show');
            select.disabled = false;
            populateSerialSelect(stagedProduct?.code);
            qty.value = 1;
            qty.readOnly = true;
        } else {
            wrap.classList.remove('show');
            select.disabled = true;
            select.innerHTML = '<option value="">Select Serial</option>';
            qty.readOnly = false;
        }
        recalcStage();
    };
    document.querySelector('#stage_by_serial').addEventListener('change', syncSerialStage);

    /* ---------- cart ---------- */
    const cartBody = document.querySelector('#cart-items');
    const emptyNote = document.querySelector('#empty-cart-note');

    const addCartRow = ({ code, name, warranty, qty, rate, discount, serial }) => {
        const tr = document.createElement('tr');
        tr.className = 'cart-row';
        tr.dataset.code = code || '';
        tr.dataset.name = name;
        tr.dataset.warranty = warranty || 0;
        tr.dataset.qty = qty;
        tr.dataset.rate = rate;
        tr.dataset.discount = discount || 0;
        tr.dataset.serial = serial || '';
        cartBody.appendChild(tr);
        renderCart();
    };

    const renderCart = () => {
        let index = 0;
        let subtotal = 0;
        cartBody.querySelectorAll('.cart-row').forEach((tr) => {
            const qty = Number(tr.dataset.qty) || 0;
            const rate = Number(tr.dataset.rate) || 0;
            const discountPct = Number(tr.dataset.discount) || 0;
            const rowSubtotal = qty * rate;
            const discountAmt = rowSubtotal * (discountPct / 100);
            const rowTotal = rowSubtotal - discountAmt;
            subtotal += rowSubtotal - discountAmt;

            index++;
            tr.innerHTML = `
                <td>${index}</td>
                <td>${tr.dataset.code || '-'}</td>
                <td>${tr.dataset.name}${tr.dataset.serial ? ' <small class="cart-serial">SN: '+tr.dataset.serial+'</small>' : ''}</td>
                <td>${tr.dataset.warranty || 0}</td>
                <td>${money(qty)}</td>
                <td>${money(rate)}</td>
                <td>${money(rowSubtotal)}</td>
                <td>${money(discountPct)}</td>
                <td>${money(discountAmt)}</td>
                <td>${money(rowTotal)}</td>
                <td><button type="button" class="remove-item" aria-label="Remove item"><i class="bi bi-trash3"></i></button></td>
                <input type="hidden" name="items[${index - 1}][product_code]" value="${tr.dataset.code || ''}">
                <input type="hidden" name="items[${index - 1}][product_name]" value="${tr.dataset.name}">
                <input type="hidden" name="items[${index - 1}][warranty_days]" value="${tr.dataset.warranty || 0}">
                <input type="hidden" name="items[${index - 1}][quantity]" value="${qty}">
                <input type="hidden" name="items[${index - 1}][rate]" value="${rate}">
                <input type="hidden" name="items[${index - 1}][discount_percent]" value="${discountPct}">
                <input type="hidden" name="items[${index - 1}][serial_no]" value="${tr.dataset.serial || ''}">
            `;
        });
        emptyNote.style.display = index === 0 ? 'block' : 'none';
        recalcTotals(subtotal);
    };

    cartBody.addEventListener('click', (event) => {
        const button = event.target.closest('.remove-item');
        if (button) {
            const tr = button.closest('tr');
            if (tr.dataset.serial) { releaseSerial(tr.dataset.code, tr.dataset.serial); }
            tr.remove();
            renderCart();
        }
    });

    document.querySelector('#add-to-cart-btn').addEventListener('click', () => {
        const name = document.querySelector('#product_search').value.trim();
        const qty = Number(document.querySelector('#stage_qty').value) || 0;
        const rate = Number(document.querySelector('#stage_rate').value) || 0;
        const bySerial = document.querySelector('#stage_by_serial').checked;
        const serial = document.querySelector('#stage_serial').value;

        if (! name || qty <= 0) {
            alert('Please select a product and enter a valid quantity.');
            return;
        }
        if (bySerial && ! serial) {
            alert('Please select a serial number.');
            return;
        }

        const code = stagedProduct?.code || '';
        addCartRow({
            code,
            name: stagedProduct?.name || name,
            warranty: document.querySelector('#stage_warranty').value,
            qty, rate,
            discount: document.querySelector('#stage_discount').value,
            serial: bySerial ? serial : '',
        });

        if (bySerial) { takeSerial(code, serial); }

        document.querySelector('#product_search').value = '';
        document.querySelector('#stage_rate').value = 0;
        document.querySelector('#stage_qty').value = 1;
        document.querySelector('#stage_warranty').value = 0;
        document.querySelector('#stage_discount').value = 0;
        document.querySelector('#stage_by_serial').checked = false;
        document.querySelector('#stage_by_serial').disabled = true;
        syncSerialStage();
        stagedProduct = null;
        recalcStage();
        document.querySelector('#product_search').focus();
    });

    /* ---------- amount details ---------- */
    const recalcTotals = (subtotal) => {
        const vatPct = Number(document.querySelector('#vat_percent').value) || 0;
        const discPct = Number(document.querySelector('#discount_percent').value) || 0;
        document.querySelector('#vat').value = money(subtotal * vatPct / 100);
        document.querySelector('#discount').value = money(subtotal * discPct / 100);

        const vat = Number(document.querySelector('#vat').value) || 0;
        const discount = Number(document.querySelector('#discount').value) || 0;
        const transport = Number(document.querySelector('#transport_cost').value) || 0;
        const total = Math.max(0, subtotal + vat - discount + transport);
        const paid = Number(document.querySelector('#paid').value) || 0;

        document.querySelector('#subtotal').textContent = money(subtotal);
        document.querySelector('#total').textContent = money(total);
        document.querySelector('#due').textContent = money(Math.max(0, total - paid));
    };

    const currentSubtotal = () => {
        let subtotal = 0;
        cartBody.querySelectorAll('.cart-row').forEach((tr) => {
            const qty = Number(tr.dataset.qty) || 0;
            const rate = Number(tr.dataset.rate) || 0;
            const discountPct = Number(tr.dataset.discount) || 0;
            subtotal += qty * rate * (1 - discountPct / 100);
        });
        return subtotal;
    };

    ['#vat_percent', '#discount_percent', '#transport_cost', '#paid'].forEach(sel => {
        document.querySelector(sel).addEventListener('input', () => recalcTotals(currentSubtotal()));
    });
    ['#vat', '#discount'].forEach(sel => {
        document.querySelector(sel).addEventListener('input', () => {
            const subtotal = currentSubtotal();
            const value = Number(document.querySelector(sel).value) || 0;
            const pctField = sel === '#vat' ? '#vat_percent' : '#discount_percent';
            document.querySelector(pctField).value = subtotal > 0 ? money(value / subtotal * 100) : 0;
            recalcTotals(subtotal);
        });
    });

    document.querySelector('#mfs_toggle').addEventListener('change', (event) => {
        document.querySelector('#paid_label').firstChild.textContent = event.target.checked ? 'Mobile Banking ' : 'Cash ';
    });

    /* ---------- prefill existing items (edit mode) ---------- */
    @if ($sale)
        @foreach ($sale->items as $item)
            addCartRow({ code: @json($item->product_code), name: @json($item->product_name), warranty: {{ (int) ($item->warranty_days ?? 0) }}, qty: {{ (float) $item->quantity }}, rate: {{ (float) $item->rate }}, discount: {{ (float) $item->discount_percent }}, serial: @json($item->serial_no) });
            @if ($item->serial_no) takeSerial(@json($item->product_code), @json($item->serial_no)); @endif
        @endforeach
    @else
        renderCart();
    @endif

    @if ($searchedBarcode !== '')
        document.querySelector('#product_search')?.focus();
    @endif
})();
</script>
@endpush
