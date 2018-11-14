<?php

use App\ReadingType;
use Illuminate\Database\Seeder;

class ReadingTypesTableSeeder extends Seeder
{
    static $TABLE_NAME = 'reading_types';
    static $TYPE_HUMIDITY = 'humidity';
    static $TYPE_PM2P5 = 'pm2.5';
    static $TYPE_PM10 = 'pm10';
    static $NOT_EXISTING_TYPE = "nacl";
    static $TYPE_TEMPERATURE = 'temperature';

    public function run()
    {
        DB::table(ReadingTypesTableSeeder::$TABLE_NAME)->insert([
            [
                'type' => ReadingTypesTableSeeder::$TYPE_HUMIDITY
            ],
            [
                'type' => ReadingTypesTableSeeder::$TYPE_PM2P5
            ],
            [
                'type' => ReadingTypesTableSeeder::$TYPE_PM10
            ],
            [
                'type' => ReadingTypesTableSeeder::$TYPE_TEMPERATURE
            ]
        ]);

    }
}

