@extends('adminlte::page')

@section('title', 'Investment Account')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Accounts Module <span>›</span> Investment Account</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-graph-up-arrow"></i> Add Investment Account</h2></div>
            <form method="POST" action="{{ route('investment-account.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label>Account Name</label><input name="account_name" required value="{{ old('account_name') }}"></div>
                <div class="field-row">
                    <div class="field"><label>Account No.</label><input name="account_no" value="{{ old('account_no') }}"></div>
                    <div class="field"><label>Account Type</label><input name="account_type" value="{{ old('account_type') }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label>Bank Name</label><input name="bank_name" value="{{ old('bank_name') }}"></div>
                    <div class="field"><label>Branch Name</label><input name="branch_name" value="{{ old('branch_name') }}"></div>
                </div>
                <div class="field"><label>Initial Balance</label><input name="initial_balance" type="number" step=".01" min="0" value="{{ old('initial_balance', 0) }}"></div>
                <div class="field"><label>Description</label><textarea name="description" rows="2">{{ old('description') }}</textarea></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Investment Account List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Name</th><th>Account No.</th><th>Bank</th><th>Balance</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($investmentAccounts as $investmentAccount)
                            <tr>
                                <td>{{ $investmentAccount->account_name }}</td>
                                <td>{{ $investmentAccount->account_no ?? '-' }}</td>
                                <td>{{ $investmentAccount->bank_name ?? '-' }}</td>
                                <td>{{ number_format($investmentAccount->initial_balance, 2) }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('investment-account.destroy', $investmentAccount) }}" onsubmit="return confirm('Delete this account?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No investment accounts added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
