@extends('adminlte::page')

@section('title', 'Cheque List')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Accounts Module <span>›</span> Cheque List</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading">
            <h2><i class="bi bi-list-ul"></i> Cheque List @if ($status !== 'all') <span class="status-badge">{{ ucfirst($status) }}</span> @endif</h2>
        </div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Cheque No</th><th>Customer</th><th>Bank</th><th>Amount</th><th>Cheque Date</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($cheques as $cheque)
                        <tr>
                            <td>{{ $cheque->cheque_no }}</td>
                            <td>{{ $cheque->customer?->name ?? '-' }}</td>
                            <td>{{ $cheque->bank_name }}</td>
                            <td>{{ number_format($cheque->amount, 2) }}</td>
                            <td>{{ $cheque->cheque_date->format('d/m/Y') }}</td>
                            <td>{{ ucfirst($cheque->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No cheques found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
