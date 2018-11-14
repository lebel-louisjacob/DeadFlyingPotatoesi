<?php

namespace Tests\Feature;
use Carbon\Carbon;
use ReadingTypesTableSeeder;
use SensorReadingsTableSeeder;
use SensorsTableSeeder;
use StationsTableSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HistoryAPITest extends TestCase
{
    use DatabaseTransactions;

    public function test__Get_history_from_station_a_sensor_and_a_reading_type__sensor_does_exist_with_the_reading_type_in_that_station__returns_a_success()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/history');

        $response->assertStatus(200);
    }

    public function test__Get_history_from_a_station_a_sensor_and_a_reading_type__the_sensor_does_not_exist_in_the_station__throws_ModelNotFoundException()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/history');

        $response->assertStatus(404);
    }

    public function test__Get_history_from_a_station_a_sensor_and_a_reading_type__station_does_not_exist__throws_ModelNotFoundException()
    {
        $station_id = StationsTableSeeder::$NON_EXISTING_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/history');

        $response->assertStatus(404);
    }

    public function test__Get_history_from_a_station_a_sensor_and_a_reading_type__station_is_private__throws_ForbiddenAccessException()
    {
        $station_id = StationsTableSeeder::$PRIVATE_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PRIVATE_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_HUMIDITY;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/history');

        $response->assertStatus(403);
    }

    public function test__Get_history_from_a_station_a_sensor_and_a_reading_type__always__returns_history_with_the_correct_format()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $expected_history_format = [
            'hour' => [],
            'day' => [],
            'week' => [],
        ];

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/history');

        $json = $response->json();
        $key_differences = array_diff_key($expected_history_format, $json);
        self::assertEquals(0, count($key_differences));
    }

    public function test__Get_history_from_a_station_a_sensor_and_a_reading_type__always__sorts_lastHour_array_by_the_oldest_date()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $aMinuteAgoDate = new Carbon('now');
        $aMinuteAgoDate->addMinutes(1);

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/history');
        $json = $response->json();
        $hourValues = $json["hour"];
        $hourKeys = array_keys($hourValues);

        foreach($hourKeys as $hourKey)
        {
            self::assertTrue($aMinuteAgoDate->diffInHours(new Carbon($hourKey)) < 1);
        }
    }

    public function test__Get_history_from_a_station_a_sensor_and_a_reading_type__always__sorts_lastDay_array_by_the_oldest_date()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $aMinuteAgoDate = new Carbon('now');
        $aMinuteAgoDate->addMinutes(1);

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/history');
        $json = $response->json();
        $dayValues = $json["day"];
        $dayKeys = array_keys($dayValues);

        foreach($dayKeys as $dayKey)
        {
            self::assertTrue($aMinuteAgoDate->diffInDays(new Carbon($dayKey)) < 1);
        }
    }

    public function test__Get_history_from_a_station_a_sensor_and_a_reading_type__always__sorts_lastWeek_array_by_the_oldest_date()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $aMinuteAgoDate = new Carbon('now');
        $aMinuteAgoDate->addMinutes(1);

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/history');
        $json = $response->json();
        $weekValues = $json["week"];
        $weekKeys = array_keys($weekValues);

        foreach($weekKeys as $weekKey)
        {
            self::assertTrue($aMinuteAgoDate->diffInWeeks(new Carbon($weekKey)) < 1);
        }
    }
}
