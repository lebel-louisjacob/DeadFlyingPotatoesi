<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-14
 * Time: 10:20 AM
 */

namespace Tests\Unit;

use App\Auth\LoginProxy;
use App\Http\Controllers\LoginController;
use App\Http\Repositories\ClientRepository;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\LoginRequest;
use App\User;
use Laravel\Passport\Client;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    const USER_ID = 1;
    const USER_NAME = 'Test';
    const USER_EMAIL = 'test@test.com';
    const USER_PASSWORD = 'secret';
    const USER_TYPE = 'admin';

    const CLIENT_ID = 1;
    const CLIENT_NAME = 'ClientTest';
    const CLIENT_SECRET = 'ClientSecret';

    private $loginProxySpy;
    private $userRepositorySpy;
    private $clientRepositorySpy;
    private $loginController;

    public function setUp()
    {
        parent::setUp();

        $user = new User([
            'id' => self::USER_ID,
            'name' => self::USER_NAME,
            'email' => self::USER_EMAIL,
            'password' => self::USER_PASSWORD,
            'type' => self::USER_TYPE,
            'remember_token' => str_random(10),
        ]);

        $client = new Client([
            'id' => self::CLIENT_ID,
            'name' => self::CLIENT_NAME,
            'secret' => self::CLIENT_SECRET
        ]);

        $this->loginProxySpy = Mockery::spy(LoginProxy::class);

        $this->userRepositorySpy = Mockery::spy(UserRepository::class);
        $this->userRepositorySpy->shouldReceive('findByField')->andReturn(collect([$user]));

        $this->clientRepositorySpy = Mockery::spy(ClientRepository::class);
        $this->clientRepositorySpy->shouldReceive('findByField')->andReturn(collect([$client]));

        $this->loginController = new LoginController($this->loginProxySpy, $this->userRepositorySpy, $this->clientRepositorySpy);
    }

    public function test_login_method_should_call_userRepository_once_with_good_email()
    {
        //arrange
        $request = new LoginRequest(['email' => self::USER_EMAIL, 'password' => self::USER_PASSWORD]);

        //act
        $this->loginController->login($request);

        //assert
        $this->userRepositorySpy->shouldHaveReceived('findByField')
            ->once()
            ->with('email', self::USER_EMAIL);
    }

    public function test_login_method_should_call_clientRepository_once()
    {
        //arrange
        $request = new LoginRequest(['email' => self::USER_EMAIL, 'password' => self::USER_PASSWORD]);

        //act
        $this->loginController->login($request);

        //assert
        $this->clientRepositorySpy->shouldHaveReceived('findByField')
            ->once();
    }

    public function test_login_method_should_call_loginProxy_once_with_good_data()
    {
        //arrange
        $request = new LoginRequest(['email' => self::USER_EMAIL, 'password' => self::USER_PASSWORD]);
        $EXPECTED_DATA = ([
            'username' => self::USER_EMAIL,
            'password' => self::USER_PASSWORD,
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'grant_type' => 'password',
            'scope' => null
        ]);

        //act
        $this->loginController->login($request);

        //assert
        $this->loginProxySpy->shouldHaveReceived('getToken')
            ->once()
            ->with($EXPECTED_DATA);
    }

    public function test_login_method_should_return_a_response_token()
    {
        //arrange
        $request = new LoginRequest(['email' => self::USER_EMAIL, 'password' => self::USER_PASSWORD]);
        $EXPECTED_TOKEN = 'Auth_Token';
        $this->loginProxySpy->shouldReceive('getToken')->andReturn($EXPECTED_TOKEN);

        //act
        $token = $this->loginController->login($request);

        //assert
        $this->assertEquals($token, response($EXPECTED_TOKEN));
    }
}