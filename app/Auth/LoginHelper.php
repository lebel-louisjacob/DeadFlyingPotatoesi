<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-13
 * Time: 9:14 AM
 */

namespace App\Auth;

use App\User;
use App\Exceptions\InvalidCredentialException;
use Illuminate\Foundation\Application;

class LoginHelper
{
    public static function getDataForProxy($user, $password, $client, $grantType)
    {
        if (!is_null($user)) {
            $data = [
                'username' => $user->email,
                'password' => $password,
                'scope' => $user->get_scope()
            ];
        }
        else {
            throw new InvalidCredentialException();
        }

        $data = array_merge($data, [
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'grant_type'    => $grantType
        ]);

        return $data;
    }
}