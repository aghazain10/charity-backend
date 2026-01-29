<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;
    public $type; // 'change' or 'reset'

    public function __construct(User $user, string $url, string $type = 'reset')
    {
        $this->user = $user;
        $this->url = $url;
        $this->type = $type;
    }

    public function build()
    {
        $subject = $this->type === 'change'
            ? 'Confirm Your Password Change'
            : 'Reset Your Password';

        return $this->subject($subject . ' - ' . config('app.name'))
            ->markdown('emails.password-reset');
    }
}
