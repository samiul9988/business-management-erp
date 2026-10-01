@extends('adminlte::page')

@section('title', 'Customer Due')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Reports <span>›</span> Customer Due</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-cash-stack"></i> Customer Due List</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Sl</th><th>Customer Id</th><th>Customer Name</th><th>Address</th><th>Area</th><th>Mobile</th><th>Due Amount</th></tr></thead>
                <tbody>
                    @forelse ($customers as $index => $customer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $customer->customer_code }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->address ?? '-' }}</td>
                            <td>{{ $customer->area?->name ?? '-' }}</td>
                            <td>{{ $customer->mobile ?? '-' }}</td>
                            <td>{{ number_format($customer->previous_due, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No customers with outstanding due.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
