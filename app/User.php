<?php

namespace App;

use App\Providers\AuthServiceProvider;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
/**
 * @SWG\Definition(
 *   definition="User",
 *   type="object",
 *   required={"name", "email", "password", "type"},
 *   @SWG\Property(property="name", type="string"),
 *   @SWG\Property(property="email", type="string"),
 *   @SWG\Property(property="password", type="string"),
 *   @SWG\Property(property="type", type="string"),
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    private $scope;

    public function comments(){
        return $this->hasMany('App\Comment');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    function get_scope() {
        return $this->scope;
    }

    function set_scope($scope) {
        $this->scope = $scope;
    }

    function changePassword($password){
        $hashed = Hash::make($password);

        DB::table('users')
            ->where('id', $this->id)
            ->update(['password' => $hashed]);
    }

    public function newFromBuilder($attributes = array(), $connection = null)
    {
        $class = 'App\Users\\'. studly_case($attributes->type);
        $model = new $class;

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }
}
