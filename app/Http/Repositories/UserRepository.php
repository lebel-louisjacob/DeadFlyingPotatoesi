<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-14
 * Time: 9:46 AM
 */

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

use Log;
use Prettus\Repository\Eloquent\BaseRepository;

class UserRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "App\\User";
    }

    public function exist($email) {
        $user = DB::table('users')->where('email', $email)->first();

        return $user != null;
    }

    public function generateResetToken($email){
        $token = $this->getExistingToken($email);
        if($token != null) return $token;

        $token = md5(uniqid(rand(), true));

        DB::table('password_resets')->insert(
            ['email' => $email, 'token' => $token]
        );

        return $token;
    }

    public function getExistingToken($email){
        $result = DB::table('password_resets')
            ->select(['token'])
            ->where('email', '=', $email)
            ->get()->pluck('token')->first();

        if($result == null) return null;

        return $result;
    }

    public function isTokenValid($token)
    {
        $result = DB::table('password_resets')
            ->select(['token'])
            ->where('token', '=', $token)
            ->get();

        return $result->count() > 0;
    }

    public function getUserFromToken($token){
        $email = DB::table('password_resets')
            ->select(['email'])
            ->where('token', '=', $token)
            ->get()->pluck('email')->first();

        if($email==null) return null;

        return $this->findByField('email', $email)->first();
    }

    public function deleteToken($token){
        DB::table('password_resets')
            ->where('token', '=', $token)
            ->delete();
    }
}