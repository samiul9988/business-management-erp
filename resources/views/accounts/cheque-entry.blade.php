@extends('adminlte::page')

@section('title', $cheque ? 'Edit Cheque' : 'Cheque Information')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Accounts Module <span>›</span> {{ $cheque ? 'Edit Cheque' : 'Cheque Entry' }}</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <section class="admin-crud-card" style="max-width:760px;">
        <div class="sales-card-heading"><h2><i class="bi bi-cash-stack"></i> {{ $cheque ? 'Edit Cheque' : 'New Cheque Entry' }}</h2></div>
        <form method="POST" action="{{ $cheque ? route('cheque.update', $cheque) : route('cheque.store') }}" class="admin-crud-form">
            @csrf
            @if ($cheque) @method('PUT') @endif
            <div class="field"><label>Select Customer</label><select name="customer_id" required><option value="">Select a Customer</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id', $cheque?->customer_id) == $customer->id)>{{ $customer->customer_code }} - {{ $customer->name }}</option>@endforeach</select></div>
            <div class="field-row">
                <div class="field"><label>Bank Name</label><input name="bank_name" required value="{{ old('bank_name', $cheque?->bank_name) }}"></div>
                <div class="field"><label>Branch Name</label><input name="branch_name" value="{{ old('branch_name', $cheque?->branch_name) }}"></div>
            </div>
            <div class="field-row">
                <div class="field"><label>Cheque No</label><input name="cheque_no" required value="{{ old('cheque_no', $cheque?->cheque_no) }}"></div>
                <div class="field"><label>Cheque Amount</label><input name="amount" type="number" step=".01" min="0" required value="{{ old('amount', $cheque?->amount) }}"></div>
            </div>
            <div class="field"><label>Cheque Status</label><select name="status" required><option value="pending" @selected(old('status', $cheque?->status ?? 'pending') === 'pending')>Pending</option><option value="paid" @selected(old('status', $cheque?->status) === 'paid')>Paid</option><option value="dishonoured" @selected(old('status', $cheque?->status) === 'dishonoured')>Dishonoured</option></select></div>
            <div class="field-row">
                <div class="field"><label>Issue Date</label><input name="issue_date" type="date" value="{{ old('issue_date', $cheque?->issue_date->toDateString() ?? now()->toDateString()) }}" required></div>
                <div class="field"><label>Cheque Date</label><input name="cheque_date" type="date" value="{{ old('cheque_date', $cheque?->cheque_date->toDateString() ?? now()->toDateString()) }}" required></div>
            </div>
            <div class="field-row">
                <div class="field"><label>Reminder Date</label><input name="reminder_date" type="date" value="{{ old('reminder_date', $cheque?->reminder_date?->toDateString()) }}"></div>
                <div class="field"><label>Submit Date</label><input name="submit_date" type="date" value="{{ old('submit_date', $cheque?->submit_date?->toDateString()) }}"></div>
            </div>
            <div class="field"><label>Description</label><input name="description" value="{{ old('description', $cheque?->description) }}"></div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> {{ $cheque ? 'Update' : 'Save' }}</button>
        </form>
    </section>
@stop
