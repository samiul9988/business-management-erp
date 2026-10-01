@extends('adminlte::page')

@section('title', 'Cash Transfer')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Accounts Module <span>›</span> Cash Transfer</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-arrow-left-right"></i> Cash Transfer</h2></div>
            <form method="POST" action="{{ route('cash-transfer.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label>Transfer Date</label><input name="transfer_date" type="date" value="{{ old('transfer_date', now()->toDateString()) }}" required></div>
                <div class="field"><label>Transfer By</label><select name="from_bank_account_id"><option value="">Cash</option>@foreach ($bankAccounts as $bankAccount)<option value="{{ $bankAccount->id }}" @selected(old('from_bank_account_id') == $bankAccount->id)>{{ $bankAccount->account_name }}</option>@endforeach</select></div>
                <div class="field"><label>Transfer To</label><select name="to_bank_account_id"><option value="">Cash</option>@foreach ($bankAccounts as $bankAccount)<option value="{{ $bankAccount->id }}" @selected(old('to_bank_account_id') == $bankAccount->id)>{{ $bankAccount->account_name }}</option>@endforeach</select></div>
                <div class="field"><label>Amount</label><input name="amount" type="number" step=".01" min="0" required></div>
                <div class="field"><label>Note</label><textarea name="note" rows="2">{{ old('note') }}</textarea></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Cash Transfer List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Date</th><th>From</th><th>To</th><th>Amount</th><th>Note</th></tr></thead>
                    <tbody>
                        @forelse ($transfers as $transfer)
                            <tr>
                                <td>{{ $transfer->transfer_date->format('d/m/Y') }}</td>
                                <td>{{ $transfer->fromAccount?->account_name ?? 'Cash' }}</td>
                                <td>{{ $transfer->toAccount?->account_name ?? 'Cash' }}</td>
                                <td>{{ number_format($transfer->amount, 2) }}</td>
                                <td>{{ $transfer->note ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No cash transfers recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
