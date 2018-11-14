<?php

use Illuminate\Database\Seeder;

class SensorsTableSeeder extends Seeder
{
    static $TABLE_NAME = 'sensors';
    static $SENSOR_FROM_PUBLIC_STATION_ID = 2;
    static $SENSOR_FROM_PRIVATE_STATION_ID = 1;
    static $NON_EXISTING_SENSOR_ID = 3;
    static $SENSOR_2_ID = 2;
    static $SENSOR_1_MODEL_ID = 1;
    static $SENSOR_2_MODEL_ID = 2;
    static $DELETED_SENSOR_MODEL_ID = 3;
    static $SENSOR_4_MODEL_ID = 3;


    public function run()
    {
        DB::table(SensorsTableSeeder::$TABLE_NAME)->insert([
            [
                'model_id' => SensorsTableSeeder::$SENSOR_2_MODEL_ID,
                'station_id' => StationsTableSeeder::$PRIVATE_STATION_ID,
                'created_at' => StationsTableSeeder::$STATION_2_DATE,
                'updated_at' => StationsTableSeeder::$STATION_2_DATE
            ],
            [
                'model_id' => SensorsTableSeeder::$SENSOR_1_MODEL_ID,
                'station_id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'created_at' => StationsTableSeeder::$STATION_1_DATE,
                'updated_at' => StationsTableSeeder::$STATION_1_DATE
            ],
            [
                'model_id' => SensorsTableSeeder::$DELETED_SENSOR_MODEL_ID,
                'station_id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'created_at' => StationsTableSeeder::$STATION_1_DATE,
                'updated_at' => StationsTableSeeder::$STATION_1_DATE
            ],
            [
                'model_id' => SensorsTableSeeder::$SENSOR_4_MODEL_ID,
                'station_id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'created_at' => StationsTableSeeder::$STATION_1_DATE,
                'updated_at' => StationsTableSeeder::$STATION_1_DATE
            ],
        ]);

        DB::table(SensorsTableSeeder::$TABLE_NAME)->where('id', SensorsTableSeeder::$NON_EXISTING_SENSOR_ID)->delete();

        factory(App\Sensor::class, 10)->create();
    }
}
