@extends('adminlte::page')

@section('title', 'Employee')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> HR & Payroll <span>›</span> Employee</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-person-badge"></i> Add Employee</h2></div>
            <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data" class="admin-crud-form">
                @csrf
                <div class="field"><label>Employee ID</label><input value="{{ $nextEmployeeCode }}" disabled></div>
                <div class="field"><label>Employee Name</label><input name="name" required value="{{ old('name') }}"></div>
                <div class="field-row">
                    <div class="field"><label>Designation</label><select name="designation_id"><option value="">-- Select --</option>@foreach ($designations as $designation)<option value="{{ $designation->id }}" @selected(old('designation_id') == $designation->id)>{{ $designation->name }}</option>@endforeach</select></div>
                    <div class="field"><label>Department</label><select name="department_id"><option value="">-- Select --</option>@foreach ($departments as $department)<option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>@endforeach</select></div>
                </div>
                <div class="field-row">
                    <div class="field"><label>Joint Date</label><input name="join_date" type="date" value="{{ old('join_date', now()->toDateString()) }}"></div>
                    <div class="field"><label>Salary Range</label><input name="salary_range" type="number" step=".01" min="0" value="{{ old('salary_range', 0) }}"></div>
                </div>
                <div class="field"><label>Activation Status</label><select name="status" required><option value="active" @selected(old('status', 'active') === 'active')>Active</option><option value="deactive" @selected(old('status') === 'deactive')>Deactive</option></select></div>
                <div class="field"><label>Present Address</label><input name="present_address" value="{{ old('present_address') }}"></div>
                <div class="field"><label>Permanent Address</label><input name="permanent_address" value="{{ old('permanent_address') }}"></div>
                <div class="field-row">
                    <div class="field"><label>Contact No</label><input name="contact_no" value="{{ old('contact_no') }}"></div>
                    <div class="field"><label>E-mail</label><input name="email" type="email" value="{{ old('email') }}"></div>
                </div>
                <div class="field"><label>Employee Image</label><input name="image" type="file" accept="image/*"></div>
                <div class="field"><label>Reference</label><textarea name="reference" rows="2">{{ old('reference') }}</textarea></div>
                <div class="field-row">
                    <div class="field"><label>Father's Name</label><input name="father_name" value="{{ old('father_name') }}"></div>
                    <div class="field"><label>Mother's Name</label><input name="mother_name" value="{{ old('mother_name') }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label>Gender</label><select name="gender"><option value="">-- Select --</option><option value="male" @selected(old('gender') === 'male')>Male</option><option value="female" @selected(old('gender') === 'female')>Female</option></select></div>
                    <div class="field"><label>Date of Birth</label><input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}"></div>
                </div>
                <div class="field"><label>Marital Status</label><select name="marital_status"><option value="">-- Select --</option><option value="married" @selected(old('marital_status') === 'married')>Married</option><option value="unmarried" @selected(old('marital_status') === 'unmarried')>Unmarried</option></select></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Employee List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Id</th><th>Name</th><th>Designation</th><th>Department</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td>{{ $employee->employee_code }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->designation?->name ?? '-' }}</td>
                                <td>{{ $employee->department?->name ?? '-' }}</td>
                                <td>{{ ucfirst($employee->status) }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('employee.destroy', $employee) }}" onsubmit="return confirm('Delete this employee?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No employees added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
