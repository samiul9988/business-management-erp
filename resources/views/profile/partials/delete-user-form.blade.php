<div class="sales-card-heading" style="margin-top:1.5rem;"><h2><i class="bi bi-exclamation-triangle-fill"></i> Delete Account</h2></div>

<p class="text-muted" style="font-size:.85rem;">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.</p>

<form method="post" action="{{ route('profile.destroy') }}" class="admin-crud-form" onsubmit="return confirm('Are you sure you want to permanently delete your account?')">
    @csrf
    @method('delete')

    <div class="field">
        <label for="delete_password" class="visually-hidden">Password</label>
        <input id="delete_password" name="password" type="password" placeholder="Password" autocomplete="current-password">
        @error('password', 'userDeletion') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Delete Account</button>
</form>
