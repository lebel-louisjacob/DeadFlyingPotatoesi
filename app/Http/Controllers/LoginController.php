<?php

namespace App\Http\Controllers;

use App\Auth\LoginHelper;
use App\Auth\LoginProxy;
use App\Http\Repositories\ClientRepository;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $loginProxy;
    private $userRepository;
    private $clientRepository;

    /**
     * Create a new controller instance.
     *
     * @param  LoginProxy  $loginProxy
     * @return void
     */
    public function __construct(LoginProxy $loginProxy, UserRepository $userRepository, ClientRepository $clientRepository)
    {
        $this->loginProxy = $loginProxy;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
    }

    public function login(LoginRequest $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $user = $this->userRepository->findByField('email', $email)->first();
        $client = $this->clientRepository->findByField('password_client', 1)->first();

        $data = LoginHelper::getDataForProxy($user, $password, $client, 'password');

        $token = $this->loginProxy->getToken($data);

        return response($token);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response(null, 204);
    }
}
