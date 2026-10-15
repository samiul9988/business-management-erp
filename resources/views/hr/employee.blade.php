@extends('adminlte::page')

@section('title', 'Employee')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> HR & Payroll <span>›</span> Employee</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif

    <section class="admin-crud-card employee-form-card">
        <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="employee-info-grid">
                <div class="employee-info-col">
                    <h3 class="employee-section-title">Job Information</h3>

                    <div class="employee-field"><label>Employee ID</label><span>:</span><input value="{{ $nextEmployeeCode }}" disabled></div>
                    <div class="employee-field"><label>Employee Name</label><span>:</span><input name="name" placeholder="Employee Name .." required value="{{ old('name') }}"></div>
                    <div class="employee-field">
                        <label>Designation</label><span>:</span>
                        <select name="designation_id"><option value="">Choose a Designation...</option>@foreach ($designations as $designation)<option value="{{ $designation->id }}" @selected(old('designation_id') == $designation->id)>{{ $designation->name }}</option>@endforeach</select>
                        <a href="{{ route('designation.index') }}" target="_blank" class="employee-quick-add" title="Add new designation"><i class="bi bi-plus-lg"></i></a>
                    </div>
                    <div class="employee-field">
                        <label>Department</label><span>:</span>
                        <select name="department_id"><option value="">Choose a Department...</option>@foreach ($departments as $department)<option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>@endforeach</select>
                        <a href="{{ route('department.index') }}" target="_blank" class="employee-quick-add" title="Add new department"><i class="bi bi-plus-lg"></i></a>
                    </div>
                    <div class="employee-field"><label>Joint Date</label><span>:</span><input name="join_date" type="date" value="{{ old('join_date', now()->toDateString()) }}"></div>
                    <div class="employee-field"><label>Salary Range</label><span>:</span><input name="salary_range" type="number" step=".01" min="0" placeholder="Salary Range .." value="{{ old('salary_range', 0) }}"></div>
                    <div class="employee-field">
                        <label>Activation Status</label><span>:</span>
                        <select name="status" required><option value="active" @selected(old('status', 'active') === 'active')>Active</option><option value="deactive" @selected(old('status') === 'deactive')>Deactive</option></select>
                    </div>
                </div>

                <div class="employee-info-col">
                    <h3 class="employee-section-title">Contact Information</h3>

                    <div class="employee-field"><label>Present Address</label><span>:</span><input name="present_address" placeholder="Present Address" value="{{ old('present_address') }}"></div>
                    <div class="employee-field"><label>Permanent Address</label><span>:</span><input name="permanent_address" placeholder="Permanent Address" value="{{ old('permanent_address') }}"></div>
                    <div class="employee-field"><label>Contact No</label><span>:</span><input name="contact_no" placeholder="Contact No" value="{{ old('contact_no') }}"></div>
                    <div class="employee-field"><label>E-mail</label><span>:</span><input name="email" type="email" placeholder="E-mail" value="{{ old('email') }}"></div>
                    <div class="employee-field"><label>Employee Image</label><span>:</span><input name="image" type="file" accept="image/*"></div>
                    <div class="employee-field"><label>Reference</label><span>:</span><textarea name="reference" rows="3" placeholder="Reference">{{ old('reference') }}</textarea></div>

                    <div class="employee-save-row"><button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save</button></div>
                </div>
            </div>

            <h3 class="employee-section-title employee-section-title-full">Personal Information</h3>
            <div class="employee-info-grid">
                <div class="employee-info-col">
                    <div class="employee-field"><label>Father's Name</label><span>:</span><input name="father_name" placeholder="Father's Name" value="{{ old('father_name') }}"></div>
                    <div class="employee-field"><label>Mother's Name</label><span>:</span><input name="mother_name" placeholder="Mother's Name" value="{{ old('mother_name') }}"></div>
                    <div class="employee-field">
                        <label>Gender</label><span>:</span>
                        <select name="gender"><option value="">Choose a Gender...</option><option value="male" @selected(old('gender') === 'male')>Male</option><option value="female" @selected(old('gender') === 'female')>Female</option></select>
                    </div>
                </div>
                <div class="employee-info-col">
                    <div class="employee-field"><label>Date of Birth</label><span>:</span><input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}"></div>
                    <div class="employee-field">
                        <label>Marital Status</label><span>:</span>
                        <select name="marital_status"><option value="">Choose a Marital Status...</option><option value="married" @selected(old('marital_status') === 'married')>Married</option><option value="unmarried" @selected(old('marital_status') === 'unmarried')>Unmarried</option></select>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <section class="admin-crud-card" style="margin-top:1rem;">
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
@stop
