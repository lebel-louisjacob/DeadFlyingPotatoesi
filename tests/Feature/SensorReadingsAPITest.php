<?php

namespace Tests\Feature;
use App\Users\Admin;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use ReadingTypesTableSeeder;
use SensorReadingsTableSeeder;
use SensorReadingTableSeeder;
use SensorsTableSeeder;
use StationsTableSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SensorReadingsAPITest extends TestCase
{
    use DatabaseTransactions;

    public function test__Get_sensorReadings_from_a_sensor_and_a_station__sensor_does_exist_in_that_station__returns_a_success()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings');

        $response->assertStatus(200);
    }

    public function test__Get_sensorReadings_from_a_sensor_and_a_station__sensor_does_not_exist__throws_ModelNotFoundException()
    {
        $station_id = StationsTableSeeder::$PRIVATE_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings');

        $response->assertStatus(404);
    }

    public function test__Get_sensorReadings_from_a_sensor_and_a_station__station_does_not_exist__throws_ModelNotFoundException()
    {
        $station_id = StationsTableSeeder::$NON_EXISTING_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings');

        $response->assertStatus(404);
    }

    public function test__Get_sensorReadings_from_a_sensor_and_a_private_station__sensor_does_exist__throws_ForbiddenAccessException()
    {
        $station_id = StationsTableSeeder::$PRIVATE_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PRIVATE_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings');

        $response->assertStatus(403);
    }

    public function test__Get_sensorReadings_from_a_sensor_and_a_station__station_does_not_have_that_sensor__throws_ModelNotFoundException()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PRIVATE_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings');

        $response->assertStatus(404);
    }

    public function test__Get_sensorReadings_from_a_sensor_and_a_station__always__returns_sensorReadings_with_the_correct_format()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $expected_sensor_reading_format = [
            'id' => 5,
            'info' => [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
                'sensor_id' => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
                'created_at' => SensorReadingsTableSeeder::$SENSOR_READING_1_DATE,
                'updated_at' => SensorReadingsTableSeeder::$SENSOR_READING_1_DATE
            ]];

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings');

        $json = $response->json();
        $actual_sensor_reading_format = $json[0];
        $key_differences = array_diff_key($expected_sensor_reading_format, $actual_sensor_reading_format);
        self::assertEquals(0, count($key_differences));
    }

    public function test__Get_sensorReadings_from_a_sensor_and_a_station__always__returns_all_sensorReadings_from_that_sensor()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $expected_sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$expected_sensor_id.'/readings');

        $json = $response->json();
        foreach($json as $element)
        {
            $attributes = $element["info"];
            self::assertEquals($expected_sensor_id, $attributes["sensor_id"]);
        }
    }

    public function test__Get_sensorReadings_from_station_a_sensor_and_a_reading_type__sensor_does_exist_with_the_reading_type_in_that_station__returns_a_success()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/readings');

        $response->assertStatus(200);
    }

    public function test__Get_sensorReadings_from_a_station_a_sensor_and_a_reading_type__the_sensor_does_not_exist_in_the_station__throws_ModelNotFoundException()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/readings');

        $response->assertStatus(404);
    }

    public function test__Get_sensorReadings_from_a_station_a_sensor_and_a_reading_type__station_does_not_exist__throws_ModelNotFoundException()
    {
        $station_id = StationsTableSeeder::$NON_EXISTING_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/readings');

        $response->assertStatus(404);
    }

    public function test__Get_sensorReadings_from_a_station_a_sensor_and_a_reading_type__station_is_private__throws_ForbiddenAccessException()
    {
        $station_id = StationsTableSeeder::$PRIVATE_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PRIVATE_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_HUMIDITY;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/readings');

        $response->assertStatus(403);
    }

    public function test__Get_sensorReadings_from_a_station_a_sensor_and_a_reading_type__always__returns_sensorReadings_with_the_correct_format()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $expected_sensor_reading_format = [
            'id' => 5,
            'info' => [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
                'sensor_id' => $sensor_id,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => $readingType,
                'created_at' => SensorReadingsTableSeeder::$SENSOR_READING_1_DATE,
                'updated_at' => SensorReadingsTableSeeder::$SENSOR_READING_1_DATE
            ]];

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/readings');

        $json = $response->json();
        $actual_sensor_reading_format = $json[0];
        $key_differences = array_diff_key($expected_sensor_reading_format, $actual_sensor_reading_format);
        self::assertEquals(0, count($key_differences));
    }

    public function test__Get_sensorReadings_from_a_station_a_sensor_and_a_reading_type__always__returns_all_sensor_readings_from_that_type()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/readings');

        $json = $response->json();
        foreach($json as $element)
        {
            $attributes = $element["info"];
            self::assertEquals($readingType, $attributes["type"]);
        }
    }

    public function test__Get_sensorReadings_from_a_station_a_sensor_and_a_reading_type__always__returns_all_sensor_readings_from_that_sensor_id()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $readingType = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get('http://revolvairapi.test/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/'.$readingType.'/readings');

        $json = $response->json();
        foreach($json as $element)
        {
            $attributes = $element["info"];
            self::assertEquals($sensor_id, $attributes["sensor_id"]);
        }
    }

    public function test__store__station_id_and_reading_are_valid__creates_a_sensor_reading()
    {
        Passport::actingAs(\App\User::find(1),[Admin::SCOPE]);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $postReadingResponse = $this->json('POST', '/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings',
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM2P5
            ]);

        $postReadingResponse->assertStatus(201);
    }


    public function test__store__user_is_not_admin__throws_ForbiddenAccessException()
    {
        Passport::actingAs(\App\User::find(2));
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $postReadingResponse = $this->json('POST', '/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings',
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);

        $postReadingResponse->assertStatus(403);
    }

    public function test__store__station_does_not_own_that_sensor__throws_ModelNotFoundException()
    {
        Passport::actingAs(\App\User::find(1),[Admin::SCOPE]);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_from_another_station_id = SensorsTableSeeder::$SENSOR_FROM_PRIVATE_STATION_ID;

        $postReadingResponse = $this->json('POST', '/api/stations/'.$station_id.'/sensors/'.$sensor_from_another_station_id.'/readings',
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
            ]);

        $postReadingResponse->assertStatus(404);
    }

    public function test__store__SensorReadingRequest_has_a_non_existing_type__throws_BadRequestException()
    {
        Passport::actingAs(\App\User::find(1),[Admin::SCOPE]);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $postReadingResponse = $this->json('POST', '/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings',
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$NOT_EXISTING_TYPE
            ]);

        $postReadingResponse->assertStatus(422);
    }

    public function test__store__SensorReadingRequest_has_a_invalid_type__throws_BadRequestException()
    {
        Passport::actingAs(\App\User::find(1),[Admin::SCOPE]);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $postReadingResponse = $this->json('POST', '/api/stations/'.$station_id.'/sensors/'.$sensor_id.'/readings',
            [
                'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
                'latitude' => StationsTableSeeder::$STATION_1_LAT,
                'longitude' => StationsTableSeeder::$STATION_1_LON,
                'type' => ReadingTypesTableSeeder::$TYPE_TEMPERATURE
            ]);

        $postReadingResponse->assertStatus(400);
    }
}
