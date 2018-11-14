<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Mail\EmailService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmailController extends Controller
{
    private $emailService;
    private $userRepository;

    public function __construct(EmailService $emailService, UserRepository $userRepository)
    {
        $this->emailService = $emailService;
        $this->userRepository = $userRepository;
    }

    public function subscribe(EmailRequest $request)
    {
        $email = $request->get('email');
        $name = $request->get('name');

        $list = $this->emailService->registerUser($email, $name);

        return response()->json($list, 201);
    }

    public function forgotPassword(ForgotPasswordRequest $request){
        $email = $request->get('email');

        if(!$this->userRepository->exist($email)){
            return response()->json(["error"=>"email not found"], 200);
        }

        $token = $this->userRepository->generateResetToken($email);

        $this->emailService->sendResetToken($email, $token);

        return response()->json(["result"=>"success"], 200);
    }
}
