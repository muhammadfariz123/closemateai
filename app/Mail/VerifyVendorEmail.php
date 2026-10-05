<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VerifyVendorEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->verificationUrl = URL::route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi email Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify_vendor',
        );
    }
}
