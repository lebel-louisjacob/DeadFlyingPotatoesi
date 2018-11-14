<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call(UserTypesSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(StationsTableSeeder::class);
        $this->call(ReadingTypesTableSeeder::class);
        $this->call(SensorModelsTableSeeder::class);
        $this->call(AffiliationReadingsTableSeeder::class);
        $this->call(SensorsTableSeeder::class);
        $this->call(SensorReadingsTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
        //DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}