@extends('adminlte::page')

@section('title', 'Supplier Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Administration <span>›</span> Supplier Entry</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-truck"></i> Add Supplier</h2></div>
            <form method="POST" action="{{ route('supplier.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label>Supplier Id</label><input value="{{ $nextSupplierCode }}" disabled></div>
                <div class="field-row">
                    <div class="field"><label for="mobile">Mobile</label><input id="mobile" name="mobile" value="{{ old('mobile') }}"></div>
                    <div class="field"><label for="name">Supplier Name</label><input id="name" name="name" required value="{{ old('name') }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="owner_name">Owner Name</label><input id="owner_name" name="owner_name" value="{{ old('owner_name') }}"></div>
                    <div class="field"><label>Supplier Mode</label><div style="display:flex; gap:1rem; padding-top:.4rem;"><label style="font-weight:400;"><input type="radio" name="mode" value="cash" style="width:auto;" @checked(old('mode', 'cash') === 'cash')> Cash</label><label style="font-weight:400;"><input type="radio" name="mode" value="credit" style="width:auto;" @checked(old('mode') === 'credit')> Credit</label></div></div>
                </div>
                <div class="field"><label for="address">Address</label><input id="address" name="address" value="{{ old('address') }}"></div>
                <div class="field-row">
                    <div class="field"><label for="email">Email</label><input id="email" name="email" value="{{ old('email') }}"></div>
                    <div class="field"><label for="previous_due">Previous Due</label><input id="previous_due" name="previous_due" type="number" step=".01" min="0" value="{{ old('previous_due', 0) }}"></div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Supplier List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Id</th><th>Name</th><th>Mobile</th><th>Mode</th><th>Due</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->supplier_code }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->mobile ?? '-' }}</td>
                                <td>{{ ucfirst($supplier->mode) }}</td>
                                <td>{{ number_format($supplier->previous_due, 2) }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('supplier.destroy', $supplier) }}" onsubmit="return confirm('Delete this supplier?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No suppliers added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
