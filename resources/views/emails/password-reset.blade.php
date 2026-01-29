@component('mail::message')
@if($type === 'change')
# Confirm Your Password Change

Hello {{ $user->name }},

You have requested to change your password. Please click the button below to confirm this change.
@else
# Reset Your Password

Hello {{ $user->name }},

You have requested to reset your password. Please click the button below to set a new password.
@endif

@component('mail::button', ['url' => $url])
@if($type === 'change')
Confirm Password Change
@else
Reset Password
@endif
@endcomponent

This link will expire in 1 hour.

@if($type === 'change')
**Security Note:** This will log you out of all devices.
@else
**Didn't request this?** If you didn't request a password reset, please ignore this email.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent