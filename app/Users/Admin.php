<?php

namespace App\Users;

use App\Providers\AuthServiceProvider;
use App\User;

class Admin extends User
{
    const SCOPE = 'admin';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        parent::set_scope(self::SCOPE);
    }
}
