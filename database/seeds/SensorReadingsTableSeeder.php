<?php

use App\SensorReading;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SensorReadingsTableSeeder extends Seeder
{
    const FIVE_MINUTES = 5;
    const TEN_MINUTES = 10;

    static $TABLE_NAME = 'sensor_readings';
    static $SENSOR_READING_1_VALUE = 5;
    static $SENSOR_READING_2_VALUE = 10;
    static $SENSOR_READING_3_VALUE = 123;
    static $SENSOR_READING_1_DATE = '2012-12-12 12:13:12.000000';
    static $SENSOR_READING_2_DATE = '2012-12-12 12:14:12.000000';
    static $SENSOR_READING_3_DATE = '2012-12-12 12:15:12.000000';

    private $currentDate;
    private $fiveMinutesAgoDate;
    private $tenMinutesAgoDate;
    private $weekAgoDate;

    public function __construct()
    {
        $this->weekAgoDate = new Carbon('now');
        $this->weekAgoDate->subWeeks(1);
        $this->currentDate = new Carbon('now');
        $this->fiveMinutesAgoDate = new Carbon('now');
        $this->fiveMinutesAgoDate->addMinutes(SensorReadingsTableSeeder::FIVE_MINUTES);
        $this->tenMinutesAgoDate = new Carbon('now');
        $this->tenMinutesAgoDate->addMinutes(SensorReadingsTableSeeder::TEN_MINUTES);
    }

    public function run()
    {
        DB::table(SensorReadingsTableSeeder::$TABLE_NAME)->insert([
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_1_VALUE,
                'sensor_id' => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM10,
                'created_at' => SensorReadingsTableSeeder::$SENSOR_READING_1_DATE,
                'updated_at' => SensorReadingsTableSeeder::$SENSOR_READING_1_DATE
            ],
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_1_VALUE,
                'sensor_id' => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM10,
                'created_at' => SensorReadingsTableSeeder::$SENSOR_READING_2_DATE,
                'updated_at' => SensorReadingsTableSeeder::$SENSOR_READING_2_DATE
            ],
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
                'sensor_id' => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
                'created_at' => $this->currentDate->toDateTimeString(),
                'updated_at' => $this->currentDate->toDateTimeString()
            ],
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_3_VALUE,
                'sensor_id' => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
                'created_at' => $this->weekAgoDate,
                'updated_at' => $this->weekAgoDate
            ],
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_1_VALUE,
                'sensor_id' => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
                'created_at' => $this->fiveMinutesAgoDate,
                'updated_at' => $this->fiveMinutesAgoDate
            ],
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
                'sensor_id' => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
                'created_at' => $this->tenMinutesAgoDate,
                'updated_at' => $this->tenMinutesAgoDate
            ],
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_1_VALUE,
                'sensor_id' => SensorsTableSeeder::$SENSOR_FROM_PRIVATE_STATION_ID,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_2_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM10,
                'created_at' => SensorReadingsTableSeeder::$SENSOR_READING_1_DATE,
                'updated_at' => SensorReadingsTableSeeder::$SENSOR_READING_1_DATE
            ],
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_1_VALUE,
                'sensor_id' => SensorsTableSeeder::$SENSOR_2_ID,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM10,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay()
            ],
        ]);

        factory(App\SensorReading::class, 500)->create();
    }
}