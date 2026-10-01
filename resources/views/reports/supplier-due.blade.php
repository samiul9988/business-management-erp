@extends('adminlte::page')

@section('title', 'Supplier Due')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Reports <span>›</span> Supplier Due</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-cash-stack"></i> Supplier Due List</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Supplier Code</th><th>Supplier Name</th><th>Owner Name</th><th>Address</th><th>Mobile</th><th>Due</th></tr></thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->supplier_code }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->owner_name ?? '-' }}</td>
                            <td>{{ $supplier->address ?? '-' }}</td>
                            <td>{{ $supplier->mobile ?? '-' }}</td>
                            <td>{{ number_format($supplier->previous_due, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No suppliers with outstanding due.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
