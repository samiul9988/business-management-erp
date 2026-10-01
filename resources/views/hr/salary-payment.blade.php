@extends('adminlte::page')

@section('title', 'Employee Salary Payment')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> HR & Payroll <span>›</span> Salary Payment</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-cash-coin"></i> Salary Payment</h2></div>
            <form method="POST" action="{{ route('salary-payment.store') }}" class="admin-crud-form" id="salary-payment-form">
                @csrf
                <input type="hidden" name="salary_id" id="salary_id">
                <div class="field"><label>Employee</label><select id="employee_id" required><option value="">Select Employee</option>@foreach ($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->employee_code }} - {{ $employee->name }}</option>@endforeach</select></div>
                <div class="field"><label>Month</label><select id="month_id" required><option value="">Select Month</option>@foreach ($months as $month)<option value="{{ $month->id }}">{{ $month->name }}</option>@endforeach</select></div>
                <div class="field"><label>Payment Method</label><select name="payment_method" id="payment_type" required><option value="cash">Cash</option><option value="bank">Bank</option></select></div>
                <div class="field" id="bank-account-field"><label>Bank Account</label><select name="bank_account_id"><option value="">-- Select --</option>@foreach ($bankAccounts as $bankAccount)<option value="{{ $bankAccount->id }}">{{ $bankAccount->account_name }}</option>@endforeach</select></div>
                <div class="field"><label>Salary</label><input id="salary-amount" disabled value="0.00"></div>
                <div class="field"><label>Due Amount</label><input id="salary-due" disabled value="0.00"></div>
                <div class="field"><label>Date</label><input name="payment_date" type="date" value="{{ old('payment_date', now()->toDateString()) }}" required></div>
                <div class="field"><label>Payment Amount</label><input name="amount" type="number" step=".01" min="0" required></div>
                <div class="field"><label>Note</label><textarea name="note" rows="2">{{ old('note') }}</textarea></div>
                <button type="submit" class="btn btn-primary" id="salary-submit" disabled><i class="bi bi-save"></i> Save</button>
                <div class="text-danger" id="no-salary-warning" style="display:none; font-size:.78rem;">No generated salary found for this employee and month.</div>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Salary Payment List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Date</th><th>Employee</th><th>Month</th><th>Amount</th><th>Method</th></tr></thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td>{{ $payment->salary?->employee?->name ?? '-' }}</td>
                                <td>{{ $payment->salary?->month?->name ?? '-' }}</td>
                                <td>{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ ucfirst($payment->payment_method) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No salary payments recorded yet.</td></tr>
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
    const salariesData = @json($salariesData);
    const employee = document.querySelector('#employee_id');
    const month = document.querySelector('#month_id');
    const salaryId = document.querySelector('#salary_id');
    const amountField = document.querySelector('#salary-amount');
    const dueField = document.querySelector('#salary-due');
    const submitButton = document.querySelector('#salary-submit');
    const warning = document.querySelector('#no-salary-warning');
    const paymentType = document.querySelector('#payment_type');
    const bankField = document.querySelector('#bank-account-field');
    const toggleBank = () => { bankField.style.display = paymentType.value === 'bank' ? '' : 'none'; };
    paymentType.addEventListener('change', toggleBank); toggleBank();

    const sync = () => {
        const key = `${employee.value}-${month.value}`;
        const match = salariesData[key];
        if (match) {
            salaryId.value = match.salary_id;
            amountField.value = match.amount.toFixed(2);
            dueField.value = match.due.toFixed(2);
            submitButton.disabled = false;
            warning.style.display = 'none';
        } else {
            salaryId.value = '';
            amountField.value = '0.00';
            dueField.value = '0.00';
            submitButton.disabled = true;
            warning.style.display = employee.value && month.value ? '' : 'none';
        }
    };
    employee.addEventListener('change', sync);
    month.addEventListener('change', sync);
})();
</script>
@endpush
