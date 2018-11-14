<?php

namespace App\Users;

use App\Providers\AuthServiceProvider;
use App\User;

class StationOwner extends User
{
    const SCOPE = 'station_owner';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        parent::set_scope(self::SCOPE);
    }
}
