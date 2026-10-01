@extends('adminlte::page')

@section('title', 'Customer Payment')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Accounts Module <span>›</span> Customer Payment</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-person-vcard"></i> Customer Payment</h2></div>
            <form method="POST" action="{{ route('customer-payment.store') }}" class="admin-crud-form" id="customer-payment-form">
                @csrf
                <div class="field"><label>Transaction Type</label><select name="transaction_type" required><option value="">Select</option><option value="payment" @selected(old('transaction_type') === 'payment')>Payment</option><option value="receive" @selected(old('transaction_type') === 'receive')>Receive</option></select></div>
                <div class="field"><label>Payment Type</label><select name="payment_type" id="payment_type" required><option value="cash" @selected(old('payment_type', 'cash') === 'cash')>Cash</option><option value="bank" @selected(old('payment_type') === 'bank')>Bank</option></select></div>
                <div class="field" id="bank-account-field"><label>Bank Account</label><select name="bank_account_id"><option value="">-- Select --</option>@foreach ($bankAccounts as $bankAccount)<option value="{{ $bankAccount->id }}" @selected(old('bank_account_id') == $bankAccount->id)>{{ $bankAccount->account_name }}</option>@endforeach</select></div>
                <div class="field"><label>Customer</label><select name="customer_id" required><option value="">Select Customer</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}" data-due="{{ $customer->previous_due }}" @selected(old('customer_id') == $customer->id)>{{ $customer->customer_code }} - {{ $customer->name }}</option>@endforeach</select></div>
                <div class="field"><label>Due</label><input id="customer-due" disabled value="0.00"></div>
                <div class="field"><label>Payment Date</label><input name="payment_date" type="date" value="{{ old('payment_date', now()->toDateString()) }}" required></div>
                <div class="field"><label>Description</label><input name="description" value="{{ old('description') }}"></div>
                <div class="field"><label>Amount</label><input name="amount" type="number" step=".01" min="0" required></div>
                <div class="field-row">
                    <div class="field"><label>Discount</label><input name="discount" type="number" step=".01" min="0" value="{{ old('discount', 0) }}"></div>
                    <div class="field"><label>%</label><input name="discount_percent" type="number" step=".01" min="0" max="100" value="{{ old('discount_percent', 0) }}"></div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Customer Payment List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Date</th><th>Customer</th><th>Type</th><th>Amount</th><th>Discount</th></tr></thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td>{{ $payment->customer?->name ?? '-' }}</td>
                                <td>{{ ucfirst($payment->transaction_type) }} ({{ ucfirst($payment->payment_type) }})</td>
                                <td>{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ number_format($payment->discount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No customer payments recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop

@push('js')
<script>
(() => {
    const form = document.querySelector('#customer-payment-form');
    const paymentType = document.querySelector('#payment_type');
    const bankField = document.querySelector('#bank-account-field');
    const toggleBank = () => { bankField.style.display = paymentType.value === 'bank' ? '' : 'none'; };
    paymentType.addEventListener('change', toggleBank); toggleBank();
    form.querySelector('[name="customer_id"]').addEventListener('change', event => {
        const option = event.target.selectedOptions[0];
        document.querySelector('#customer-due').value = Number(option?.dataset.due || 0).toFixed(2);
    });
})();
</script>
@endpush
