@extends('adminlte::page')

@section('title', 'Cash Transaction')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Accounts Module <span>›</span> Cash Transaction</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-cash"></i> Cash Transaction</h2></div>
            <form method="POST" action="{{ route('cash-transaction.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label>Transaction Type</label><select name="type" required><option value="">Select</option><option value="receive" @selected(old('type') === 'receive')>Cash Receive</option><option value="payment" @selected(old('type') === 'payment')>Cash Payment</option></select></div>
                <div class="field"><label>Account</label><select name="transaction_account_id" required><option value="">Select Account</option>@foreach ($accounts as $account)<option value="{{ $account->id }}" @selected(old('transaction_account_id') == $account->id)>{{ $account->name }}</option>@endforeach</select></div>
                <div class="field"><label>Date</label><input name="transaction_date" type="date" value="{{ old('transaction_date', now()->toDateString()) }}" required></div>
                <div class="field"><label>Description</label><input name="description" value="{{ old('description') }}"></div>
                <div class="field"><label>Amount</label><input name="amount" type="number" step=".01" min="0" required></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Cash Transaction List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Date</th><th>Type</th><th>Account</th><th>Description</th><th>Amount</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->type === 'receive' ? 'Cash Receive' : 'Cash Payment' }}</td>
                                <td>{{ $transaction->account?->name ?? '-' }}</td>
                                <td>{{ $transaction->description ?? '-' }}</td>
                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('cash-transaction.destroy', $transaction) }}" onsubmit="return confirm('Delete this transaction?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No cash transactions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
