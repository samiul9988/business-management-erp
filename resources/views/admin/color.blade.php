@extends('adminlte::page')

@section('title', 'Color Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Administration <span>›</span> Color Entry</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-palette"></i> Add Color</h2></div>
            <form method="POST" action="{{ route('color.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label for="name">Color Name</label><input id="name" name="name" required value="{{ old('name') }}"></div>
                @error('name') <div class="text-danger" style="font-size:.75rem">{{ $message }}</div> @enderror
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Color List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Sl No</th><th>Color Name</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($colors as $index => $color)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <form method="POST" action="{{ route('color.update', $color) }}" style="display:flex; gap:.4rem;">
                                        @csrf @method('PUT')
                                        <input name="name" value="{{ $color->name }}" style="padding:.3rem .5rem; border:1px solid #cad8e5; border-radius:4px;">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                    </form>
                                </td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('color.destroy', $color) }}" onsubmit="return confirm('Delete this color?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No colors added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
