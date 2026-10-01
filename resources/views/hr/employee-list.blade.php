@extends('adminlte::page')

@section('title', 'Employee List')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> HR & Payroll <span>›</span> Employee List</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Employee List @if ($status !== 'all') <span class="status-badge">{{ ucfirst($status) }}</span> @endif</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Id</th><th>Name</th><th>Designation</th><th>Department</th><th>Contact</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee->employee_code }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->designation?->name ?? '-' }}</td>
                            <td>{{ $employee->department?->name ?? '-' }}</td>
                            <td>{{ $employee->contact_no ?? '-' }}</td>
                            <td>{{ ucfirst($employee->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No employees found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
