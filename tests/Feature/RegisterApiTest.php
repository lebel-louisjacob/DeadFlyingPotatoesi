<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-26
 * Time: 9:04 AM
 */

namespace Tests\Feature;


use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterApiTest extends TestCase
{
    use DatabaseTransactions;

    const VALID_EMAIL = 'aGoodEmail@test.com';
    const VALID_NAME = 'Cédric Toupin';
    const VALID_PASSWORD = 'secret';
    const INVALID_EMAIL = 'notAGoodEmail@.com';
    const INVALID_NAME = '';
    const INVALID_PASSWORD = '';

    public function setUp()
    {
        parent::setUp();
    }

    public function test_register_should_return_error_if_invalid_email()
    {
        $response = $this->json('POST', '/api/register', ['email' => self::INVALID_EMAIL, 'name' => self::VALID_NAME, 'password' => self::VALID_PASSWORD]);

        $response->assertJsonValidationErrors('email');
    }

    public function test_register_should_return_error_if_invalid_name()
    {
        $response = $this->json('POST', '/api/register', ['email' => self::VALID_EMAIL, 'name' => self::INVALID_NAME, 'password' => self::VALID_PASSWORD]);

        $response->assertJsonValidationErrors('name');
    }

    public function test_register_should_return_error_if_invalid_password()
    {
        $response = $this->json('POST', '/api/register', ['email' => self::VALID_EMAIL, 'name' => self::VALID_NAME, 'password' => self::INVALID_PASSWORD]);

        $response->assertJsonValidationErrors('password');
    }

    public function test_register_with_good_credential_return_success_response()
    {
        $response = $this->json('POST', '/api/register',
            ['email' => self::VALID_EMAIL, 'name' => self::VALID_NAME, 'password' => self::VALID_PASSWORD]);

        $response->assertSuccessful();
    }

    public function test_register_with_already_existing_email_return_error()
    {
        $this->json('POST', '/api/register',
            ['email' => self::VALID_EMAIL, 'name' => self::VALID_NAME, 'password' => self::VALID_PASSWORD]);
        $response = $this->json('POST', '/api/register',
            ['email' => self::VALID_EMAIL, 'name' => self::VALID_NAME, 'password' => self::VALID_PASSWORD]);

        $response->assertStatus(422);
    }

    public function test_register_with_good_credential_return_the_newly_created_user()
    {
        $response = $this->json('POST', '/api/register',
            ['email' => self::VALID_EMAIL, 'name' => self::VALID_NAME, 'password' => self::VALID_PASSWORD]);

        $response->assertJson(['email' => self::VALID_EMAIL, 'name' => self::VALID_NAME]);
    }
}