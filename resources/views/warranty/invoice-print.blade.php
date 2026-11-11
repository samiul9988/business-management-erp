<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Warranty Invoice {{ $warranty->invoice_no }}</title>
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
        .signatures { display: flex; justify-content: space-between; margin-top: 24px; }
        .signatures span { display: inline-block; border-top: 1px solid #000; padding-top: 4px; min-width: 160px; text-align: center; background: rgba(255, 255, 255, .8); }
        .invoice-policy { margin-top: 10px; font-size: 9.5px; line-height: 1.4; background: rgba(255, 255, 255, .88); }
        .invoice-policy-bottom { padding: 4px 8px; border: 1px solid #000; }
        .status-chip { display: inline-block; padding: 1px 8px; border-radius: 10px; background: #eef9fc; font-weight: 600; }
        .invoice-actions { width: 794px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: flex-end; gap: 1rem; }
        .invoice-actions button { padding: .5rem 1.1rem; border: 0; border-radius: 4px; background: #1769ed; color: #fff; font-size: .85rem; cursor: pointer; }
        .note-block { margin-top: 10px; font-size: 12px; }
        .barcode-block { position: absolute; top: 16px; right: 52px; text-align: center; }
        .barcode-block img { height: 46px; }
        .barcode-block div { font-size: 10px; letter-spacing: .03em; margin-top: 2px; }
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
                <img src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($warranty->invoice_no) }}&code=Code128&dpi=96" alt="Barcode">
            </div>
            <hr class="invoice-rule">
            <hr class="invoice-rule-thin">
            <h1 class="invoice-title">Warranty Invoice</h1>
            <hr class="invoice-rule-thin below">

            <div class="invoice-meta">
                <div>
                    <p><strong>Invoice No.:</strong> {{ $warranty->invoice_no }}</p>
                    <p><strong>Name:</strong> {{ $warranty->customer_name }}</p>
                    <p><strong>Mobile:</strong> {{ $warranty->customer_mobile ?: '-' }}</p>
                    <p><strong>Status:</strong> <span class="status-chip">{{ ucfirst(str_replace('_', ' ', $warranty->warranty_status)) }}</span></p>
                </div>
                <table class="meta-right-table">
                    <tr><td class="meta-label"><strong>Date:</strong></td><td class="meta-value">{{ $warranty->warranty_date->format('Y-m-d') }}</td></tr>
                    <tr><td class="meta-label"><strong>Est. Delivery:</strong></td><td class="meta-value">{{ $warranty->estimated_delivery_date?->format('Y-m-d') ?? '-' }}</td></tr>
                    <tr><td class="meta-label"><strong>Received By:</strong></td><td class="meta-value">{{ $warranty->receivedByEmployee?->name ?? '-' }}</td></tr>
                </table>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Serial No</th>
                        <th class="num">Qty</th>
                        <th>Sale Out Date</th>
                        <th>Problem</th>
                        <th>Condition</th>
                        <th class="num">Validity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $warranty->product_name ?: '-' }}</td>
                        <td>{{ $warranty->serial_no ?: '-' }}</td>
                        <td class="num">{{ number_format((float) $warranty->quantity, 2) }}</td>
                        <td>{{ $warranty->sale_out_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $warranty->problem ?: '-' }}</td>
                        <td>{{ $warranty->condition ?: '-' }}</td>
                        <td class="num">{{ $warranty->warranty_validity ? 'Valid' : 'Void' }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Transfer By</th>
                        <th>Transfer Date</th>
                        <th>Receive By (SR)</th>
                        <th>Return Received By</th>
                        <th>Return Condition</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $warranty->supplier?->name ?? '-' }}</td>
                        <td>{{ $warranty->transferByEmployee?->name ?? '-' }}</td>
                        <td>{{ $warranty->transfer_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $warranty->receive_by_sr ?: '-' }}</td>
                        <td>{{ $warranty->returnReceivedByEmployee?->name ?? '-' }}</td>
                        <td>{{ $warranty->return_condition ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Claim Status</th>
                        <th>Received Date</th>
                        <th>Delivered By</th>
                        <th>Delivered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $warranty->claim_status ?: '-' }}</td>
                        <td>{{ $warranty->received_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $warranty->deliveredByEmployee?->name ?? '-' }}</td>
                        <td>{{ $warranty->delivered_date?->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            @if ($warranty->note || $warranty->remarks)
                <div class="note-block">
                    @if ($warranty->note)<p><strong>Note:</strong> {{ $warranty->note }}</p>@endif
                    @if ($warranty->remarks)<p><strong>Remarks:</strong> {{ $warranty->remarks }}</p>@endif
                </div>
            @endif

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
