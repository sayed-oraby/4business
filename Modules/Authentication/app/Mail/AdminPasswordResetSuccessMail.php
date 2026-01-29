<?php

namespace Modules\Authentication\Mail;

use Modules\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPasswordResetSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user
    ) {
    }

    public function build(): self
    {
        return $this->subject(__('authentication::messages.emails.success.subject', ['app' => config('app.name')]))
            ->view('authentication::emails.password-reset-success')
            ->with([
                'user' => $this->user,
            ]);
    }
}
