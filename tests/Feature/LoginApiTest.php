<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-12
 * Time: 11:39 AM
 */

namespace Tests\Feature;

use Tests\TestCase;
use UsersTableSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginApiTest extends TestCase
{
    use DatabaseTransactions;

    const VALID_EMAIL = 'aGoodEmail@test.com';
    const VALID_PASSWORD = 'secret';
    const INVALID_EMAIL = 'notAGoodEmail@.com';
    const INVALID_PASSWORD = '';

    public function setUp()
    {
        parent::setUp();
        $this->artisan('passport:client', ['--password' => null, '--no-interaction' => true]);
    }

    public function test_login_should_return_error_if_invalid_email()
    {
        $response = $this->json('POST', '/api/login', ['email' => self::INVALID_EMAIL, 'password' => self::VALID_PASSWORD]);

        $response->assertJsonValidationErrors('email');
    }

    public function test_login_should_return_error_if_invalid_password()
    {
        $response = $this->json('POST', '/api/login', ['email' => self::VALID_EMAIL, 'password' => self::INVALID_PASSWORD]);

        $response->assertJsonValidationErrors('password');
    }

    public function test_login_with_good_credential_return_success_response()
    {
        $response = $this->json('POST', '/api/login',
            ['email' => UsersTableSeeder::ADMIN_EMAIL, 'password' => UsersTableSeeder::PASSWORD]);

        $response->assertSuccessful();
    }

    public function test_login_with_bad_credential_return_error_response()
    {
        $response = $this->json('POST', '/api/login',
            ['email' => self::VALID_EMAIL, 'password' => self::VALID_PASSWORD]);

        $response->assertStatus(404);
    }

    public function test_login_with_good_credential_return_a_token_and_a_expired_timer()
    {
        $response = $this->json('POST', '/api/login',
            ['email' => UsersTableSeeder::ADMIN_EMAIL, 'password' => UsersTableSeeder::PASSWORD]);

        $response->assertJsonStructure(['access_token', 'expires_in']);
    }

    public function test_logout_with_good_token_return_success_response()
    {
        $response = $this->json('POST', '/api/login',
            ['email' => UsersTableSeeder::ADMIN_EMAIL, 'password' => UsersTableSeeder::PASSWORD]);
        $token = json_decode($response->content())->access_token;

        $logout_response = $this->json('POST', '/api/logout',
            ['email' => UsersTableSeeder::ADMIN_EMAIL, 'password' => UsersTableSeeder::PASSWORD],
            ['HTTP_Authorization' => 'Bearer ' . $token]);

        $logout_response->assertSuccessful();
    }
}