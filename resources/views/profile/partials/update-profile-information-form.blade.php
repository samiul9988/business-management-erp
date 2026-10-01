<div class="sales-card-heading"><h2><i class="bi bi-person-fill"></i> Profile Information</h2></div>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="admin-crud-form">
    @csrf
    @method('patch')

    <div class="field">
        <label for="name">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="mt-2 text-sm">
                Your email address is unverified.
                <button form="send-verification" type="submit" class="btn btn-link p-0 align-baseline">Click here to re-send the verification email.</button>
            </p>

            @if (session('status') === 'verification-link-sent')
                <p class="text-success">A new verification link has been sent to your email address.</p>
            @endif
        @endif
    </div>

    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
</form>
