<div class="sales-card-heading"><h2><i class="bi bi-shield-lock-fill"></i> Update Password</h2></div>

<form method="post" action="{{ route('password.update') }}" class="admin-crud-form">
    @csrf
    @method('put')

    <div class="field">
        <label for="update_password_current_password">Current Password</label>
        <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password">
        @error('current_password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="field">
        <label for="update_password_password">New Password</label>
        <input id="update_password_password" name="password" type="password" autocomplete="new-password">
        @error('password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="field">
        <label for="update_password_password_confirmation">Confirm Password</label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
        @error('password_confirmation', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
</form>
