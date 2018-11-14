<?php

use App\UserType;
use Illuminate\Database\Seeder;

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserType::create([
            'type' => 'admin',
        ]);

        UserType::create([
            'type' => 'station_owner',
        ]);
    }
}
