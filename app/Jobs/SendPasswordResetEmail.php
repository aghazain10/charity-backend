<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\PasswordResetMail;

class SendPasswordResetEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $token;
    public $type; // 'change' or 'reset'

    public function __construct(User $user, string $token, string $type = 'reset')
    {
        $this->user = $user;
        $this->token = $token;
        $this->type = $type;
    }

    public function handle()
    {
        // Determine the URL based on type
        if ($this->type === 'change') {
            // For change password (logged in user)
            $url = config('app.frontend_url') . '/auth/confirm-password/' . $this->token;
        } else {
            // For forgot password (reset)
            $url = config('app.frontend_url') . '/auth/reset-password/' . $this->token;
        }

        // Send email with type-specific content
        Mail::to($this->user->email)->send(new PasswordResetMail(
            $this->user,
            $url,
            $this->type
        ));
    }
}
