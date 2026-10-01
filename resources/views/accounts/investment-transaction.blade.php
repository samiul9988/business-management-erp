@extends('adminlte::page')

@section('title', 'Investment Transactions')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Accounts Module <span>›</span> Investment Transactions</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-graph-up-arrow"></i> Investment Transaction</h2></div>
            <form method="POST" action="{{ route('investment-transaction.store') }}" class="admin-crud-form" id="investment-transaction-form">
                @csrf
                <div class="field"><label>Transaction Date</label><input name="transaction_date" type="date" value="{{ old('transaction_date', now()->toDateString()) }}" required></div>
                <div class="field"><label>Account</label><select name="investment_account_id" required><option value="">Select Account</option>@foreach ($investmentAccounts as $investmentAccount)<option value="{{ $investmentAccount->id }}" @selected(old('investment_account_id') == $investmentAccount->id)>{{ $investmentAccount->account_name }}</option>@endforeach</select></div>
                <div class="field"><label>Transaction Type</label><select name="transaction_type" required><option value="">Select</option><option value="receive" @selected(old('transaction_type') === 'receive')>Receive</option><option value="payment" @selected(old('transaction_type') === 'payment')>Payment</option></select></div>
                <div class="field"><label>Payment Type</label><select name="payment_type" id="payment_type" required><option value="cash" @selected(old('payment_type', 'cash') === 'cash')>Cash</option><option value="bank" @selected(old('payment_type') === 'bank')>Bank</option></select></div>
                <div class="field" id="bank-account-field"><label>Bank Account</label><select name="bank_account_id"><option value="">-- Select --</option>@foreach ($bankAccounts as $bankAccount)<option value="{{ $bankAccount->id }}" @selected(old('bank_account_id') == $bankAccount->id)>{{ $bankAccount->account_name }}</option>@endforeach</select></div>
                <div class="field"><label>Amount</label><input name="amount" type="number" step=".01" min="0" required></div>
                <div class="field"><label>Note</label><textarea name="note" rows="2">{{ old('note') }}</textarea></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Investment Transaction List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Date</th><th>Account</th><th>Type</th><th>Amount</th><th>Note</th></tr></thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->investmentAccount?->account_name ?? '-' }}</td>
                                <td>{{ ucfirst($transaction->transaction_type) }} ({{ ucfirst($transaction->payment_type) }})</td>
                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ $transaction->note ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No investment transactions recorded yet.</td></tr>
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
    const paymentType = document.querySelector('#payment_type');
    const bankField = document.querySelector('#bank-account-field');
    const toggleBank = () => { bankField.style.display = paymentType.value === 'bank' ? '' : 'none'; };
    paymentType.addEventListener('change', toggleBank); toggleBank();
})();
</script>
@endpush
