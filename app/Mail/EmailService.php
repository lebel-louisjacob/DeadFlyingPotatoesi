<?php

namespace App\Mail;

use Bogardo\Mailgun\Contracts\Mailgun;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    private $mgClient;
    private $listAdress;

    public function __construct(Mailgun $mailgun)
    {
        $this->mgClient = $mailgun;
        $this->listAdress = 'testing_list@'.env('MAILGUN_DOMAIN');
    }

    public function registerUser($email, $name)
    {
        $response = $this->mgClient->api()->post("lists/{$this->listAdress}/members", [
            'address'      => $email,
            'name'         => $name,
            'subscribed'   => 'yes'
        ]);

        return $response;
    }

    public function sendResetToken($email, $token)
    {
        Mail::to($email)->send(new ForgotPasswordInformation($token));
    }
}