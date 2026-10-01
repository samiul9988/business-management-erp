@extends('adminlte::page')

@section('title', 'Repair Company Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Repair Module <span>›</span> Repair Company</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-building-gear"></i> Add Repair Company</h2></div>
            <form method="POST" action="{{ route('repair-company.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label>Name</label><input name="name" required value="{{ old('name') }}"></div>
                @error('name') <div class="text-danger" style="font-size:.75rem">{{ $message }}</div> @enderror
                <div class="field"><label>Description</label><textarea name="description" rows="2">{{ old('description') }}</textarea></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Repair Company List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Sl No</th><th>Name</th><th>Description</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($companies as $index => $company)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $company->name }}</td>
                                <td>{{ $company->description ?? '-' }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('repair-company.destroy', $company) }}" onsubmit="return confirm('Delete this company?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No repair companies added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
