<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-21
 * Time: 10:20 AM
 */

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmailApiTest extends TestCase
{
    use DatabaseTransactions;

    const VALID_EMAIL = 'aGoodEmail@test.com';
    const VALID_NAME = 'Cédric Toupin';
    const INVALID_EMAIL = 'notAGoodEmail@.com';
    const INVALID_NAME = '';

    public function setUp()
    {
        parent::setUp();
    }

    public function test_subscribe_should_return_error_if_invalid_email()
    {
        $response = $this->json('POST', '/api/mail/subscribe', ['email' => self::INVALID_EMAIL, 'password' => self::VALID_NAME]);

        $response->assertJsonValidationErrors('email');
    }

    public function test_login_should_return_error_if_invalid_name()
    {
        $response = $this->json('POST', '/api/mail/subscribe', ['email' => self::VALID_EMAIL, 'password' => self::INVALID_NAME]);

        $response->assertJsonValidationErrors('name');
    }

    /* Les test d'api ne peuvent pas être fait pour le moment puisque nous ne pouvons pas gérer les entrées dans la base de données de MailGun */
    public function test_emailApi()
    {
        $this->assertTrue(true);
    }
}