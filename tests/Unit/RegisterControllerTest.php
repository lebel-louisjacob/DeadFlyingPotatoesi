<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-23
 * Time: 11:38 AM
 */

namespace Tests\Unit;


use App\Http\Controllers\RegisterController;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\RegisterRequest;
use App\User;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterControllerTest extends TestCase
{
    use DatabaseTransactions;

    const USER_ID = 1;
    const USER_NAME = 'Test';
    const USER_EMAIL = 'test@test.com';
    const USER_PASSWORD = 'secret';
    const USER_TYPE = 'station_owner';

    private $userRepositorySpy;
    private $registerController;
    private $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = new User([
            'id' => self::USER_ID,
            'name' => self::USER_NAME,
            'email' => self::USER_EMAIL,
            'password' => self::USER_PASSWORD,
            'type' => self::USER_TYPE,
            'remember_token' => str_random(10),
        ]);

        $this->userRepositorySpy = Mockery::spy(UserRepository::class);

        $this->registerController = new RegisterController($this->userRepositorySpy);
    }

    public function test_register_method_should_call_userRepository_once_with_good_request()
    {
        //arrange
        $request = new RegisterRequest(['email' => self::USER_EMAIL, 'name' => self::USER_NAME, 'password' => self::USER_PASSWORD]);

        //expect
        $this->userRepositorySpy->shouldReceive('create')->andReturn([$this->user]);

        //act
        $this->registerController->register($request);

        //assert
        $this->userRepositorySpy->shouldHaveReceived('create')
            ->once()
            ->with($request->all());
    }

    public function test_register_method_should_return_created_when_user_is_created()
    {
        //arrange
        $request = new RegisterRequest(['email' => self::USER_EMAIL, 'name' => self::USER_NAME, 'password' => self::USER_PASSWORD]);

        //expect
        $this->userRepositorySpy->shouldReceive('create')->andReturn([$this->user]);

        //act
        $response = $this->registerController->register($request);

        //assert
        $this->assertEquals(201, $response->status());
    }
}