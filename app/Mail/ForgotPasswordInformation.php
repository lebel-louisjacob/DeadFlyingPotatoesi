<?php

namespace App\Mail;

use App\Http\Repositories\StationRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPasswordInformation extends Mailable
{
    use Queueable, SerializesModels;

    private $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('test@hotmail.com', 'Revolvair Test')
            ->subject("Réinitialisation de votre mot de passe")
            ->view('emails.forgotPassword')
            ->with([
                'resetPasswordLink' => env('FRONT_END_URL') . 'resetpassword/' . $this->token
            ]);
    }
}
