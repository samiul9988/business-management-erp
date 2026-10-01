@extends('adminlte::page')

@section('title', 'Add Account')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Accounts Module <span>›</span> Transaction Accounts</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-journal-text"></i> Add Account</h2></div>
            <form method="POST" action="{{ route('account.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label for="name">Account Name</label><input id="name" name="name" required value="{{ old('name') }}"></div>
                <div class="field"><label for="type">Account Type</label><select id="type" name="type" required><option value="">Select Account Type</option><option value="cash_in" @selected(old('type') === 'cash_in')>Cash In</option><option value="cash_out" @selected(old('type') === 'cash_out')>Cash Out</option></select></div>
                <div class="field"><label for="description">Description</label><textarea id="description" name="description" rows="2">{{ old('description') }}</textarea></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Account List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Id</th><th>Name</th><th>Type</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($accounts as $account)
                            <tr>
                                <td>{{ $account->id }}</td>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->type === 'cash_in' ? 'Cash In' : 'Cash Out' }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('account.destroy', $account) }}" onsubmit="return confirm('Delete this account?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No accounts added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
