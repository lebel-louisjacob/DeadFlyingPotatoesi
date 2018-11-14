<?php
/**
 * Created by PhpStorm.
 * User: Dom
 * Date: 2018-02-28
 * Time: 10:11
 */

namespace App\Http\Controllers;


use App\Http\Repositories\UserRepository;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ValidateResetTokenRequest;

class UserController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validateResetToken(ValidateResetTokenRequest $request){
        $token = $request->get('token');

        $isValid = $this->userRepository->isTokenValid($token);

        return response()->json(["valid"=>$isValid], 200);
    }

    public function resetPassword(ResetPasswordRequest $request){
        $token = $request->get('token');
        $password = $request->get('password');

        if(!$this->userRepository->isTokenValid($token)){
            return response()->json(["error"=>"invalid token"], 200);
        }

        $user = $this->userRepository->getUserFromToken($token);

        $user->changePassword($password);
        $this->userRepository->deleteToken($token);

        return response()->json(["result"=>"success"], 200);
    }
}