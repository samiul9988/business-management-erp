@extends('adminlte::page')

@section('title', 'Month')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> HR & Payroll <span>›</span> Month</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-calendar3"></i> Add Month</h2></div>
            <form method="POST" action="{{ route('month.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label for="name">Month Name</label><input id="name" name="name" type="month" required value="{{ old('name') }}"></div>
                @error('name') <div class="text-danger" style="font-size:.75rem">{{ $message }}</div> @enderror
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Month List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Sl No</th><th>Month</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($months as $index => $month)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $month->name }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('month.destroy', $month) }}" onsubmit="return confirm('Delete this month?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No months added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
