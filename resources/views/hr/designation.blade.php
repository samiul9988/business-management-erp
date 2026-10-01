@extends('adminlte::page')

@section('title', 'Add Designation')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> HR & Payroll <span>›</span> Designation</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-briefcase"></i> Add Designation</h2></div>
            <form method="POST" action="{{ route('designation.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label for="name">Designation Name</label><input id="name" name="name" required value="{{ old('name') }}"></div>
                @error('name') <div class="text-danger" style="font-size:.75rem">{{ $message }}</div> @enderror
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Designation List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Sl No</th><th>Designation Name</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($designations as $index => $designation)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $designation->name }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('designation.destroy', $designation) }}" onsubmit="return confirm('Delete this designation?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No designations added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
