<?php

namespace Tests\Unit;

use App\Http\Controllers\UserController;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ValidateResetTokenRequest;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $userController;
    private $userRepositoryMock;
    private $userMock;

    const TEST_TOKEN = "123456";
    const NEW_PASSWORD = "qwerty";

    public function setUp()
    {
        parent::setUp();

        $this->userRepositoryMock = Mockery::mock(UserRepository::class);
        $this->userMock = Mockery::mock(User::class);
        $this->userRepositoryMock->shouldReceive('deleteToken')->andReturn();

        $this->userController = new UserController($this->userRepositoryMock);
    }

    public function test_validateResetToken_isValid_returnsTrue(){
        // Arrange
        $request = new ValidateResetTokenRequest(['token' => self::TEST_TOKEN]);

        $this->userRepositoryMock
            ->shouldReceive('isTokenValid')
            ->with(self::TEST_TOKEN)
            ->andReturn(true);

        // Act
        $response = $this->userController->validateResetToken($request);
        $content = $response->getContent();

        // Assert
        $this->assertEquals('{"valid":true}', $content);
    }

    public function test_validateResetToken_isNotValid_returnsTrue(){
        // Arrange
        $request = new ValidateResetTokenRequest(['token' => self::TEST_TOKEN]);

        $this->userRepositoryMock
            ->shouldReceive('isTokenValid')
            ->with(self::TEST_TOKEN)
            ->andReturn(false);

        // Act
        $response = $this->userController->validateResetToken($request);
        $content = $response->getContent();

        // Assert
        $this->assertEquals('{"valid":false}', $content);
    }

    public function test_resetPassword_invalidToken_returnsError(){
        // Arrange
        $request = new ResetPasswordRequest(["token" => self::TEST_TOKEN, "password" => self::NEW_PASSWORD]);
        $this->userRepositoryMock->shouldReceive("isTokenValid")->with(self::TEST_TOKEN)->andReturn(false);

        // Act
        $response = $this->userController->resetPassword($request);
        $content = $response->getContent();

        // Assert
        $this->assertEquals('{"error":"invalid token"}', $content);
    }

    public function test_resetPassword_calls_getUserFromToken(){
        // Arrange
        $request = new ResetPasswordRequest(["token" => self::TEST_TOKEN, "password" => self::NEW_PASSWORD]);

        $this->userRepositoryMock->shouldReceive("isTokenValid")->with(self::TEST_TOKEN)->andReturn(true);

        // Expect
        $this->userMock->shouldReceive('changePassword')->andReturn();
        $this->userRepositoryMock->shouldReceive("getUserFromToken")->with(self::TEST_TOKEN)->andReturn($this->userMock);

        // Act
        $this->userController->resetPassword($request);
    }

    public function test_resetPassword_resetsUserPassword(){
        // Arrange
        $request = new ResetPasswordRequest(["token" => self::TEST_TOKEN, "password" => self::NEW_PASSWORD]);

        $this->userRepositoryMock->shouldReceive("isTokenValid")->with(self::TEST_TOKEN)->andReturn(true);
        $this->userRepositoryMock->shouldReceive("getUserFromToken")->with(self::TEST_TOKEN)->andReturn($this->userMock);

        // Expect
        $this->userMock->shouldReceive('changePassword')->andReturn();

        // Act
        $this->userController->resetPassword($request);

        // Assert
    }
}