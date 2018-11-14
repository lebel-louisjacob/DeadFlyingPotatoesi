<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
use App\Http\Requests\RegisterRequest;
use App\Users\StationOwner;
use Illuminate\Support\Facades\Hash;
use Prettus\Validator\Exceptions\ValidatorException;

class RegisterController extends Controller
{
    private $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterRequest $request)
    {
        $request->merge(['type' => StationOwner::SCOPE]);
        $request['password'] = Hash::make($request['password']);

        $newUser = $this->userRepository->create($request->all());

        return response()->json($newUser, 201);
    }
}
