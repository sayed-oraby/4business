<?php

namespace Modules\Authentication\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPasswordResetOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected string $otp;
    protected string $email;
    protected string $userLocale;

    public function __construct(string $otp, string $email, ?string $locale = null)
    {
        $this->otp = $otp;
        $this->email = $email;
        $this->userLocale = $locale ?? app()->getLocale();
    }

    public function build(): self
    {
        $locale = $this->userLocale;
        
        return $this->subject(__('authentication::messages.emails.otp.subject', [], $locale))
            ->view('authentication::emails.password-otp')
            ->with([
                'otp' => $this->otp,
                'email' => $this->email,
                'expires' => config('authentication.password.otp_ttl', 10),
                'locale' => $locale,
            ]);
    }
}
