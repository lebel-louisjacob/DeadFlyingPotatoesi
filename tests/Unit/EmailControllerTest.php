<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-21
 * Time: 8:40 AM
 */

namespace Tests\Unit;

use App\Http\Controllers\EmailController;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Mail\EmailService;
use Bogardo\Mailgun\Contracts\Mailgun;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmailControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $mailGunMock;
    private $emailService;
    private $emailController;
    private $userRepositoryMock;

    const TEST_NAME = 'Test';
    const TEST_EMAIL = 'test@test.com';

    public function setUp()
    {
        parent::setUp();

        $this->userRepositoryMock = Mockery::mock(UserRepository::class);

        $this->mailGunMock = Mockery::spy(Mailgun::class);
        $this->mailGunMock->shouldReceive('api')->andReturn($this->mailGunMock);

        $this->emailService = Mockery::Mock(EmailService::class, [$this->mailGunMock])->makePartial();

        $this->emailController = new EmailController($this->emailService, $this->userRepositoryMock);
    }

    public function test_register_method_should_call_mailGun_once_with_good_name_and_email()
    {
        //arrange
        $request = new EmailRequest(['email' => self::TEST_EMAIL, 'name' => self::TEST_NAME]);
        $listAdress = 'testing_list@'.env('MAILGUN_DOMAIN');
        $EXPECTED_URL = "lists/{$listAdress}/members";
        $EXPECTED_DATA = $newEmail = [
            'address'      => self::TEST_EMAIL,
            'name'         => self::TEST_NAME,
            'subscribed'   => 'yes'
        ];

        //expect
        $this->mailGunMock->shouldReceive('post')->andReturn();

        //act
        $this->emailController->subscribe($request);

        //assert
        $this->mailGunMock->shouldHaveReceived('post')
            ->once()
            ->with($EXPECTED_URL, $EXPECTED_DATA);
    }

    public function test_register_method_should_return_successfulResponse_when_called_with_good_name_and_email()
    {
        //arrange
        $request = new EmailRequest(['email' => self::TEST_EMAIL, 'name' => self::TEST_NAME]);

        //expect
        $this->mailGunMock->shouldReceive('post')->andReturn();

        //act
        $response = $this->emailController->subscribe($request);

        //assert
        $this->assertEquals(201, $response->status());
    }

    public function test_forgotPassword_emailDoesntExist_throwsNotFound(){
        // Arrange
        $request = new ForgotPasswordRequest(['email' => self::TEST_EMAIL]);
        $this->userRepositoryMock->shouldReceive('exist')->andReturn(false);

        // Act
        $response = $this->emailController->forgotPassword($request);
        $content = $response->getContent();

        // Assert
        $this->assertEquals('{"error":"email not found"}', $content);

    }

    public function test_forgotPassword_emailExists_generatesToken(){
        // Arrange
        $request = new ForgotPasswordRequest(['email' => self::TEST_EMAIL]);
        $this->userRepositoryMock->shouldReceive('exist')->andReturn(true);

        $this->emailService->shouldReceive('sendResetToken')->andReturn();

        // Expect
        $this->userRepositoryMock->shouldReceive('generateResetToken')->andReturn();

        // Act
        $this->emailController->forgotPassword($request);
    }

    public function test_forgotPassword_emailExists_sendsEmailWithToken(){
        // Arrange
        $token = "123456";

        $request = new ForgotPasswordRequest(['email' => self::TEST_EMAIL]);
        $this->userRepositoryMock->shouldReceive('exist')->andReturn(true);
        $this->userRepositoryMock->shouldReceive('generateResetToken')->andReturn($token);

        // Expect
        $this->emailService->shouldReceive('sendResetToken')
            ->withArgs([self::TEST_EMAIL, $token])
            ->andReturn();

        // Act
        $this->emailController->forgotPassword($request);
    }
}