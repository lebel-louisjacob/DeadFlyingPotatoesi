<?php

use Illuminate\Database\Seeder;

class StationsTableSeeder extends Seeder
{
    static $TABLE_NAME = 'stations';
    static $PUBLIC_STATION_NAME = "Station du Cégep de Sainte-Foy";
    static $PRIVATE_STATION_NAME = "Station Carré d'Youville";
    static $STATION_3_NAME = "Station L'Ancienne-Lorette";
    static $STATION_1_CITY = "Quebec";
    static $STATION_2_CITY = "Quebec";
    static $STATION_3_CITY = "Montreal";
    static $STATION_1_LAT = 46.7841866;
    static $STATION_1_LON = -71.2852583;
    static $STATION_2_LAT = 46.812541;
    static $STATION_2_LON = -71.213354;
    static $STATION_3_LAT = 46.818716;
    static $STATION_3_LON = -71.360415;
    static $STATION_IS_PUBLIC = 0;
    static $STATION_IS_PRIVATE = 1;
    static $STATION_3_PUBLIC = 0;
    static $STATION_1_DATE = '2012-12-12 12:12:12.000000';
    static $STATION_2_DATE = '2018-12-18 18:18:18.000000';
    static $STATION_3_DATE = '2018-12-18 18:18:18.000000';
    static $PUBLIC_STATION_ID = 1;
    static $PRIVATE_STATION_ID = 2;
    static $NON_EXISTING_STATION_ID = 3;


    public function run()
    {
        DB::table(StationsTableSeeder::$TABLE_NAME)->insert([
            [
                'name' => StationsTableSeeder::$PUBLIC_STATION_NAME,
                'city' => StationsTableSeeder::$STATION_1_CITY,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'is_private' => StationsTableSeeder::$STATION_IS_PUBLIC,
                'created_at' => StationsTableSeeder::$STATION_1_DATE,
                'updated_at' => StationsTableSeeder::$STATION_1_DATE
            ],
            [
                'name' => StationsTableSeeder::$PRIVATE_STATION_NAME,
                'city' => StationsTableSeeder::$STATION_2_CITY,
                'latitude' => StationsTableSeeder::$STATION_2_LAT,
                'longitude' => StationsTableSeeder::$STATION_2_LON,
                'is_private' => StationsTableSeeder::$STATION_IS_PRIVATE,
                'created_at' => StationsTableSeeder::$STATION_2_DATE,
                'updated_at' => StationsTableSeeder::$STATION_2_DATE
            ],
            [
                'name' => StationsTableSeeder::$STATION_3_NAME,
                'city' => StationsTableSeeder::$STATION_3_CITY,
                'latitude' => StationsTableSeeder::$STATION_3_LAT,
                'longitude' => StationsTableSeeder::$STATION_3_LON,
                'is_private' => StationsTableSeeder::$STATION_3_PUBLIC,
                'created_at' => StationsTableSeeder::$STATION_3_DATE,
                'updated_at' => StationsTableSeeder::$STATION_3_DATE
            ]
        ]);

        DB::table(StationsTableSeeder::$TABLE_NAME)->where('id', StationsTableSeeder::$NON_EXISTING_STATION_ID)->delete();

        factory(App\Station::class, 4)->create();
    }
}
