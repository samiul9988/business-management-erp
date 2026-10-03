<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; background: #e9e9e9; font-family: Arial, Helvetica, sans-serif; color: #111; font-size: 13px; }

        .invoice-sheet {
            position: relative;
            width: 794px;
            min-height: 1123px;
            margin: 0 auto;
            background: #fff url('{{ asset('images/invoice-letterhead.png') }}') no-repeat top center / 794px 1123px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .invoice-content { position: relative; padding: 148px 52px 110px; }

        h1.invoice-title { text-align: center; font-size: 19px; margin: 0 0 16px; letter-spacing: .03em; }
        .invoice-meta { display: flex; justify-content: space-between; gap: 2rem; margin-bottom: 14px; }
        .invoice-meta div p { margin: 2px 0; }
        .meta-right-table { border-collapse: collapse; margin-left: auto; }
        .meta-right-table td { padding: 1px 0; white-space: nowrap; }
        .meta-right-table td.meta-label { text-align: left; padding-right: 6px; }
        .meta-right-table td.meta-value { text-align: left; width: 100%; }
        table.items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.items-table th, table.items-table td { border: 1px solid #000; padding: 5px 7px; font-size: 12.5px; }
        table.items-table th { background: rgba(243, 243, 243, .85); text-align: left; }
        table.items-table td { background: rgba(255, 255, 255, .78); }
        table.items-table td.num, table.items-table th.num { text-align: right; }
        table.items-table .serial-line { display: block; color: #444; font-size: 11.5px; margin-top: 2px; }
        .items-total-row td { font-weight: bold; }
        .invoice-bottom { display: flex; justify-content: space-between; gap: 2rem; margin-top: 6px; }
        .amount-words { max-width: 320px; }
        .totals-table { width: 320px; border-collapse: collapse; }
        .totals-table td { padding: 3px 0; font-size: 13px; }
        .totals-table td.label { color: #222; }
        .totals-table td.value { text-align: right; font-variant-numeric: tabular-nums; }
        .totals-table tr.rule td { border-top: 1px solid #000; padding-top: 6px; }
        .totals-table tr.grand td, .totals-table tr.net td { font-weight: bold; }
        .qr-block { text-align: center; margin: 28px 0 24px; }
        .qr-block img { width: 110px; height: 110px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 24px; }
        .signatures span { display: inline-block; border-top: 1px solid #000; padding-top: 4px; min-width: 160px; text-align: center; background: rgba(255, 255, 255, .8); }
        .invoice-actions { width: 794px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .invoice-actions button { padding: .5rem 1.1rem; border: 0; border-radius: 4px; background: #1769ed; color: #fff; font-size: .85rem; cursor: pointer; }
        .share-link-bar { display: flex; align-items: center; gap: .5rem; font-size: .8rem; }
        .share-link-bar input { width: 280px; padding: .35rem .5rem; border: 1px solid #ccc; border-radius: 4px; font-size: .78rem; color: #444; }
        .share-link-bar button { padding: .35rem .6rem; border: 1px solid #1769ed; border-radius: 4px; background: #fff; color: #1769ed; font-size: .78rem; cursor: pointer; }
        .copy-toast { font-size: .78rem; color: #1a9d53; font-weight: 600; opacity: 0; transition: opacity .2s ease; }
        .copy-toast.show { opacity: 1; }
        .share-link-footer { margin-top: 14px; font-size: 10px; color: #555; background: rgba(255, 255, 255, .8); display: inline-block; }
        .share-link-footer a { color: #1769ed; }
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-actions { display: none; }
            .invoice-sheet { width: auto; min-height: 0; }
        }
    </style>
</head>
<body>
    @php $shareUrl = route('sales.invoice.public', $sale->share_token); @endphp

    <div class="invoice-actions">
        <div class="share-link-bar">
            <span>Share link:</span>
            <input type="text" id="share-link-input" value="{{ $shareUrl }}" readonly onclick="this.select()">
            <button type="button" id="share-link-copy">Copy</button>
            <span class="copy-toast" id="share-link-toast">Copied!</span>
        </div>
        <button type="button" onclick="window.print()">Print Invoice</button>
    </div>

    <div class="invoice-sheet">
        <div class="invoice-content">
            <h1 class="invoice-title">Sales Invoice</h1>

            <div class="invoice-meta">
                <div>
                    <p><strong>Invoice No.:</strong> {{ $sale->invoice_no }}</p>
                    @if ($customer?->customer_code)
                        <p><strong>Customer ID:</strong> {{ $customer->customer_code }}</p>
                    @endif
                    <p><strong>Name:</strong> {{ $sale->customer_name }}</p>
                    <p><strong>Mobile:</strong> {{ $sale->customer_mobile ?: '-' }}</p>
                    <p><strong>Address:</strong> {{ $sale->customer_address ?: ($customer->address ?? '-') }}</p>
                </div>
                <table class="meta-right-table">
                    <tr><td class="meta-label"><strong>Date:</strong></td><td class="meta-value">{{ $sale->sale_date->format('Y-m-d') }} {{ $sale->created_at?->format('h:i a') }}</td></tr>
                    <tr><td class="meta-label"><strong>Sold by:</strong></td><td class="meta-value">{{ $sale->user->name ?? '-' }}</td></tr>
                    <tr><td class="meta-label"><strong>Saved by:</strong></td><td class="meta-value">{{ $sale->user->name ?? '-' }}</td></tr>
                    <tr><td class="meta-label"><strong>Last edit:</strong></td><td class="meta-value">{{ $sale->user->name ?? '-' }}</td></tr>
                </table>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Description</th>
                        <th class="num">Wnty</th>
                        <th class="num">Qnty</th>
                        <th class="num">Unit Price</th>
                        <th class="num">Dis.</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{ $item->product_name }}
                                @if ($item->product_code)
                                    <span class="serial-line">Code: {{ $item->product_code }}</span>
                                @endif
                            </td>
                            <td class="num">{{ $item->warranty_days ? $item->warranty_days.' Days' : '0 Days' }}</td>
                            <td class="num">{{ number_format((float) $item->quantity, 0) }} PCS</td>
                            <td class="num">{{ number_format((float) $item->rate, 2) }}</td>
                            <td class="num">{{ number_format((float) $item->discount_amount, 2) }}</td>
                            <td class="num">{{ number_format((float) $item->total, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="items-total-row">
                        <td colspan="3">Total</td>
                        <td class="num">{{ number_format((float) $sale->items->sum('quantity'), 0) }}</td>
                        <td></td>
                        <td class="num">{{ number_format((float) $sale->items->sum('discount_amount'), 2) }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            @php
                $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                $amountWords = ucwords($formatter->format((int) round((float) $sale->total))) . ' Tk only';
            @endphp

            <div class="invoice-bottom">
                <div class="amount-words"><strong>Amount in Words:</strong> {{ $amountWords }}</div>

                <table class="totals-table">
                    <tr><td class="label">Sub Total</td><td class="value">{{ number_format((float) $sale->subtotal, 2) }}</td></tr>
                    <tr><td class="label">Discount (-)</td><td class="value">{{ number_format((float) $sale->discount, 2) }}</td></tr>
                    <tr><td class="label">VAT (+)</td><td class="value">{{ number_format((float) $sale->vat, 2) }}</td></tr>
                    <tr><td class="label">Transport Cost (+)</td><td class="value">{{ number_format((float) $sale->transport_cost, 2) }}</td></tr>
                    <tr class="rule grand"><td class="label">Grand Total</td><td class="value">{{ number_format((float) $sale->total, 2) }}</td></tr>
                    <tr><td class="label">Previous Due</td><td class="value">{{ number_format((float) ($customer->previous_due ?? 0), 2) }}</td></tr>
                    <tr class="rule net"><td class="label">Net Payable Amount</td><td class="value">{{ number_format((float) $sale->total + (float) ($customer->previous_due ?? 0), 2) }}</td></tr>
                    <tr><td class="label">Cash Paid</td><td class="value">{{ number_format((float) $sale->paid, 2) }}</td></tr>
                    <tr><td class="label">Bank Paid</td><td class="value">0.00</td></tr>
                    <tr class="rule"><td class="label">Current Due Amount</td><td class="value">{{ number_format((float) $sale->due, 2) }}</td></tr>
                </table>
            </div>

            <div class="qr-block">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($shareUrl) }}" alt="Invoice QR code">
            </div>

            <div class="signatures">
                <span>Received by</span>
                <span>Authorized by</span>
            </div>

            <p class="share-link-footer">View this invoice online: <a href="{{ $shareUrl }}">{{ $shareUrl }}</a></p>
        </div>
    </div>

    @if (request()->boolean('print'))
        <script>window.addEventListener('load', () => window.print());</script>
    @endif

    <script>
        (() => {
            const input = document.getElementById('share-link-input');
            const button = document.getElementById('share-link-copy');
            const toast = document.getElementById('share-link-toast');

            const fallbackCopy = (text) => {
                const temp = document.createElement('textarea');
                temp.value = text;
                temp.style.position = 'fixed';
                temp.style.opacity = '0';
                document.body.appendChild(temp);
                temp.focus();
                temp.select();
                const copied = document.execCommand('copy');
                temp.remove();
                return copied;
            };

            const showToast = () => {
                toast.classList.add('show');
                clearTimeout(showToast.timer);
                showToast.timer = setTimeout(() => toast.classList.remove('show'), 1800);
            };

            button.addEventListener('click', async () => {
                const text = input.value;
                let copied = false;

                if (navigator.clipboard?.writeText && window.isSecureContext) {
                    try {
                        await navigator.clipboard.writeText(text);
                        copied = true;
                    } catch (e) {
                        copied = false;
                    }
                }

                if (! copied) {
                    copied = fallbackCopy(text);
                }

                if (copied) {
                    showToast();
                }
            });
        })();
    </script>
</body>
</html>
