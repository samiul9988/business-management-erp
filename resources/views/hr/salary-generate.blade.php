@extends('adminlte::page')

@section('title', 'Salary Generate')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> HR & Payroll <span>›</span> Salary Generate</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-cash-stack"></i> Salary Generate</h2></div>
            <form method="POST" action="{{ route('salary-generate.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label>Select Month</label><select name="month_id" required><option value="">Select Month</option>@foreach ($months as $month)<option value="{{ $month->id }}">{{ $month->name }}</option>@endforeach</select></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Generate</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Generated Salaries</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Employee</th><th>Month</th><th>Amount</th><th>Paid</th><th>Due</th></tr></thead>
                    <tbody>
                        @forelse ($generated as $salary)
                            <tr>
                                <td>{{ $salary->employee?->name ?? '-' }}</td>
                                <td>{{ $salary->month?->name ?? '-' }}</td>
                                <td>{{ number_format($salary->amount, 2) }}</td>
                                <td>{{ number_format($salary->paid, 2) }}</td>
                                <td>{{ number_format($salary->due, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No salaries generated yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
