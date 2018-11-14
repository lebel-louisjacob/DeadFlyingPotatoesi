<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-07
 * Time: 10:09 AM
 */

namespace App\Auth;

use App\Exceptions\InvalidCredentialException;
use Illuminate\Foundation\Application;

class LoginProxy
{
    private $apiConsumer;

    public function __construct(Application $app) {

        $this->apiConsumer = $app->make('apiconsumer');
    }

    /**
     * Proxy a request to the OAuth server.
     *
     * @param array $data the data to send to the server
     */
    public function getToken(array $data = [])
    {
        $response = $this->apiConsumer->post('/oauth/token', $data);

        if (!$response->isSuccessful()) {
            throw new InvalidCredentialException();
        }

        $data = json_decode($response->getContent());

        return [
            'access_token' => $data->access_token,
            'expires_in' => $data->expires_in
        ];
    }
}