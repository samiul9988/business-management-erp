<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Repair Invoice {{ $repair->invoice_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; background: #e9e9e9; font-family: Arial, Helvetica, sans-serif; color: #111; font-size: 13px; }

        .invoice-sheet {
            display: flex;
            flex-direction: column;
            width: 794px;
            min-height: 1123px;
            margin: 0 auto;
            background: #fff;
        }
        .invoice-header-img, .invoice-footer-img { display: block; width: 100%; height: auto; flex: 0 0 auto; }
        .invoice-footer-img { margin-top: auto; }
        .invoice-content { position: relative; padding: 10px 52px; flex: 0 0 auto; }

        .invoice-rule { border: 0; border-top: 3px double #000; margin: 0 0 3px; }
        .invoice-rule-thin { border: 0; border-top: 1px dashed #000; margin: 0; }
        .invoice-rule-thin.below { margin: 10px 0 14px; }
        h1.invoice-title { text-align: center; font-size: 19px; margin: 10px 0 0; letter-spacing: .03em; }
        .invoice-meta { display: flex; justify-content: space-between; gap: 2rem; margin-bottom: 14px; }
        .invoice-meta div p { margin: 2px 0; }
        .meta-right-table { border-collapse: collapse; margin-left: auto; }
        .meta-right-table td { padding: 1px 0; white-space: nowrap; }
        .meta-right-table td.meta-label { text-align: left; padding-right: 6px; }
        .meta-right-table td.meta-value { text-align: left; width: 100%; }
        table.items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.items-table th, table.items-table td { border: 1px solid #000; padding: 3px 7px; font-size: 12px; }
        table.items-table th { background: rgba(243, 243, 243, .85); text-align: left; }
        table.items-table td { background: rgba(255, 255, 255, .78); }
        table.items-table td.num, table.items-table th.num { text-align: center; }
        .invoice-bottom { display: flex; justify-content: space-between; gap: 2rem; margin-top: 6px; }
        .amount-words { max-width: 320px; }
        .totals-table { width: 320px; border-collapse: collapse; }
        .totals-table td { padding: 3px 0; font-size: 13px; }
        .totals-table td.label { color: #222; }
        .totals-table td.value { text-align: right; font-variant-numeric: tabular-nums; }
        .totals-table tr.rule td { border-top: 1px solid #000; padding-top: 6px; }
        .totals-table tr.grand td, .totals-table tr.net td { font-weight: bold; }
        .barcode-block { position: absolute; top: 16px; right: 52px; text-align: center; }
        .barcode-block img { height: 46px; }
        .barcode-block div { font-size: 10px; letter-spacing: .03em; margin-top: 2px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 24px; }
        .signatures span { display: inline-block; border-top: 1px solid #000; padding-top: 4px; min-width: 160px; text-align: center; background: rgba(255, 255, 255, .8); }
        .invoice-policy { margin-top: 10px; font-size: 9.5px; line-height: 1.4; background: rgba(255, 255, 255, .88); }
        .invoice-policy-bottom { padding: 4px 8px; border: 1px solid #000; }
        .status-chip { display: inline-block; padding: 1px 8px; border-radius: 10px; background: #eef9fc; font-weight: 600; }
        .invoice-actions { width: 794px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: flex-end; gap: 1rem; }
        .invoice-actions button { padding: .5rem 1.1rem; border: 0; border-radius: 4px; background: #1769ed; color: #fff; font-size: .85rem; cursor: pointer; }
        @page { size: A4; margin: 0; }
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-actions { display: none; }
            .invoice-sheet { width: auto; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="invoice-actions">
        <button type="button" onclick="window.print()">Print Invoice</button>
    </div>

    <div class="invoice-sheet">
        <img src="{{ asset('images/invoice-header.png') }}" alt="" class="invoice-header-img">
        <div class="invoice-content">
            <div class="barcode-block">
                <img src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($repair->invoice_no) }}&code=Code128&dpi=96" alt="Barcode">
            </div>
            <hr class="invoice-rule">
            <hr class="invoice-rule-thin">
            <h1 class="invoice-title">Repair Invoice</h1>
            <hr class="invoice-rule-thin below">

            <div class="invoice-meta">
                <div>
                    <p><strong>Invoice No.:</strong> {{ $repair->invoice_no }}</p>
                    <p><strong>Name:</strong> {{ $repair->customer_name }}</p>
                    <p><strong>Mobile:</strong> {{ $repair->customer_mobile ?: '-' }}</p>
                    <p><strong>Status:</strong> <span class="status-chip">{{ ucfirst(str_replace('_', ' ', $repair->status)) }}</span></p>
                </div>
                <table class="meta-right-table">
                    <tr><td class="meta-label"><strong>Date:</strong></td><td class="meta-value">{{ $repair->repair_date->format('Y-m-d') }}</td></tr>
                    <tr><td class="meta-label"><strong>Exp. Delivery:</strong></td><td class="meta-value">{{ $repair->expected_delivery_date?->format('Y-m-d') ?? '-' }}</td></tr>
                    <tr><td class="meta-label"><strong>Assigned To:</strong></td><td class="meta-value">{{ $repair->assignedEmployee?->name ?? '-' }}</td></tr>
                </table>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Serial No</th>
                        <th>Problem</th>
                        <th>Condition</th>
                        <th class="num">Warranty</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $repair->product_name ?: '-' }}</td>
                        <td>{{ $repair->brand ?: '-' }}</td>
                        <td>{{ $repair->model ?: '-' }}</td>
                        <td>{{ $repair->serial_no ?: '-' }}</td>
                        <td>{{ $repair->problem ?: '-' }}</td>
                        <td>{{ $repair->condition ?: '-' }}</td>
                        <td class="num">{{ $repair->warranty_period ? 'Valid' : 'Void' }}</td>
                    </tr>
                </tbody>
            </table>

            @if ($repair->parts_name || (float) $repair->parts_purchase_amount > 0)
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Parts Used</th>
                            <th>Supplier</th>
                            <th class="num">Qty</th>
                            <th class="num">Rate</th>
                            <th class="num">Warranty (Days)</th>
                            <th class="num">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $repair->parts_name ?: '-' }}</td>
                            <td>{{ $repair->partsSupplier?->name ?? '-' }}</td>
                            <td class="num">{{ number_format((float) $repair->parts_quantity, 2) }}</td>
                            <td class="num">{{ number_format((float) $repair->parts_purchase_rate, 2) }}</td>
                            <td class="num">{{ $repair->parts_warranty_days ?? 0 }}</td>
                            <td class="num">{{ number_format((float) $repair->parts_purchase_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            @php
                $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                $amountWords = ucwords($formatter->format((int) round((float) $repair->total))) . ' Tk only';
            @endphp

            <div class="invoice-bottom">
                <div class="amount-words"><strong>Amount in Words:</strong> {{ $amountWords }}</div>

                <table class="totals-table">
                    <tr><td class="label">Sub Total</td><td class="value">{{ number_format((float) $repair->subtotal, 2) }}</td></tr>
                    <tr><td class="label">Discount (-)</td><td class="value">{{ number_format((float) $repair->discount, 2) }}</td></tr>
                    <tr class="rule grand"><td class="label">Grand Total</td><td class="value">{{ number_format((float) $repair->total, 2) }}</td></tr>
                    <tr><td class="label">Payment Method</td><td class="value">{{ strtoupper($repair->payment_method) }}</td></tr>
                    <tr><td class="label">Paid</td><td class="value">{{ number_format((float) $repair->paid, 2) }}</td></tr>
                    <tr class="rule net"><td class="label">Due Amount</td><td class="value">{{ number_format((float) $repair->due, 2) }}</td></tr>
                </table>
            </div>

            <div class="signatures">
                <span>Received by</span>
                <span>Authorized by</span>
            </div>

            <div class="invoice-policy">
                <div class="invoice-policy-bottom">
                    Warranty void: sticker removed items, burned case and physically damaged goods. No warranty for Mouse, Keyboard, Speaker, Power Supply, Adapter, Remote, Printer head, Toner Cartridge, Belt &amp; any kind of repaired item.
                </div>
            </div>
        </div>
        <img src="{{ asset('images/invoice-footer.png') }}" alt="" class="invoice-footer-img">
    </div>

    @if (request()->boolean('print'))
        <script>window.addEventListener('load', () => window.print());</script>
    @endif
</body>
</html>
