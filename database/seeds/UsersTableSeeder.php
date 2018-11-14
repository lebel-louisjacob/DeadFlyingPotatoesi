<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    const ADMIN_EMAIL = 'admin@test.com';
    const STATION_OWNER_EMAIL = 'owner@test.com';
    const STATION_OWNER_DOMJ_EMAIL = 'dominic.jobin@gmail.com';
    const PASSWORD = 'secret';

    public function run()
    {
        $password = Hash::make(self::PASSWORD);

        User::create([
            'name' => 'Administrator',
            'email' => self::ADMIN_EMAIL,
            'password' => $password,
            'type' => 'admin',
            'remember_token' => str_random(10),
        ]);

        User::create([
            'name' => 'Station-Owner',
            'email' => self::STATION_OWNER_EMAIL,
            'password' => $password,
            'type' => 'station_owner',
            'remember_token' => str_random(10),
        ]);

        User::create([
            'name' => 'Station-Owner-DomJ',
            'email' => self::STATION_OWNER_DOMJ_EMAIL,
            'password' => $password,
            'type' => 'station_owner',
            'remember_token' => str_random(10),
        ]);
    }
}