<?php

namespace Tests\Feature;

use SensorsTableSeeder;
use StationsTableSeeder;
use Tests\TestCase;
use App\SensorReading;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SensorsAPITest extends TestCase
{
    use DatabaseTransactions;

    const URL = 'http://revolvairapi.test/api/stations';

    public function test__Get_sensors_from_a_station__station_is_public__returns_a_success()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors');

        $response->assertStatus(200);
    }

    public function test__Get_sensors_from_a_station__station_is_private__throws_forbidden_access()
    {
        $station_id = StationsTableSeeder::$PRIVATE_STATION_ID;

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors');

        $response->assertStatus(403);
    }

    public function test__Get_sensors_from_a_station__returns_sensors_with_the_correct_format()
    {
        $expected_number_of_differences = 0;
        $expected_station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $expected_sensor_format = [
            'id' => 1,
            'info' => [
                'name' => "Sensor PM",
                'station id' => 1,
                'types' => [
                    '0' => "PM 2.5",
                    '1' => "PM 10"
                ],
                'range' => 19.2
            ]
        ];

        $response = $this->get(SensorsAPITest::URL . '/' .$expected_station_id . '/sensors');

        $json = $response->json();
        $actual_sensor_format = $json[0];
        $key_differences = array_diff_key($expected_sensor_format, $actual_sensor_format);
        self::assertEquals($expected_number_of_differences, count($key_differences));
    }

    public function test__Get_sensors_from_a_station_returns_all_sensors_from_that_station()
    {
        $expected_station_id = StationsTableSeeder::$PUBLIC_STATION_ID;

        $response = $this->get(SensorsAPITest::URL . '/' . $expected_station_id . '/sensors');

        $json = $response->json();
        foreach($json as $element)
        {
            $info = $element["info"];
            self::assertEquals($expected_station_id, $info["station_id"]);
        }
    }

    public function test__Get_sensor_from_the_sensor_id_and_a_public_station_id__sensor_exists__returns_a_success()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors/' . $sensor_id);

        $response->assertStatus(200);
    }

    public function test__Get_sensor_from_the_sensor_id_and_a_private_station_id__sensor_exists__throws_ForbiddenAccessException()
    {
        $station_id = strval(StationsTableSeeder::$PRIVATE_STATION_ID);
        $sensor_id = strval(SensorsTableSeeder::$SENSOR_FROM_PRIVATE_STATION_ID);

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors/' . $sensor_id);

        $response->assertStatus(403);
    }

    public function test__Get_sensor_from_the_sensor_id_and_the_its_station_id__returns_sensors_with_the_correct_format()
    {
        $expected_station_id = strval(StationsTableSeeder::$PUBLIC_STATION_ID);
        $sensor_id = strval(SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID);
        $expected_sensor_format = [
            'id' => 1,
            'info' => [
                'name' => "Sensor PM",
                'station id' => 1,
                'types' => [
                    '0' => "PM 2.5",
                    '1' => "PM 10"
                ],
                'range' => 19.2
            ]
        ];

        $response = $this->get(SensorsAPITest::URL . '/' . $expected_station_id . '/sensors/' . $sensor_id);

        $json = $response->json();
        $actual_sensor_format = $json;
        $key_differences = array_diff_key($expected_sensor_format, $actual_sensor_format);
        self::assertEquals(0, count($key_differences));
    }

    public function test__Get_sensor_from_the_sensor_id_and_its_station_id__returns_the_sensors_with_the_correct_station_id()
    {
        $expected_station_id = strval(StationsTableSeeder::$PUBLIC_STATION_ID);
        $sensor_id = strval(SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID);

        $url = SensorsAPITest::URL . '/' . $expected_station_id . '/sensors/' . $sensor_id;

        $response = $this->get($url);

        $json = $response->json();

        $info = $json["info"];
        self::assertEquals($expected_station_id, $info["station_id"]);
    }

    public function test__Get_sensor_from_the_sensor_id_and_a_station_id__the_sensor_id_does_not_exist__throws_ModuleNotFoundException()
    {
        $expected_result = 'Resource not found';
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors/' . $sensor_id);

        $json = $response->json();
        self::assertEquals($json['error'], $expected_result);
    }

    public function test__Get_sensor_from_the_sensor_id_and_a_station_id__the_sensor_id_does_not_exist__returns_code_404()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors/' . $sensor_id);

        $response->assertStatus(404);
    }

    public function test__Get_sensor_from_the_sensor_id_and_a_station_id__the_station_does_not_have_the_sensor__throws_ModuleNotFoundException()
    {
        $expected_result = 'Resource not found';
        $station_id = strval(StationsTableSeeder::$PUBLIC_STATION_ID);
        $sensor_id = strval(SensorsTableSeeder::$SENSOR_FROM_PRIVATE_STATION_ID);

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors/' . $sensor_id);

        $json = $response->json();
        self::assertEquals($json['error'], $expected_result);
    }

    public function test__Get_sensor_latest_values_from_the_sensor_id_and_a_station_id__the_sensor_id_does_not_exist__throws_ModuleNotFoundException()
    {
        $expected_result = 'Resource not found';
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors/' . $sensor_id . '/readings/latest-values');

        $json = $response->json();
        self::assertEquals($json['error'], $expected_result);
    }

    public function test__Get_sensor_latest_values_from_the_sensor_id_and_a_station_id__the_sensor_id_does_not_exist__returns_code_404()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors/' . $sensor_id . '/readings/latest-values');

        $response->assertStatus(404);
    }

    public function test__Get_sensor_latest_values_from_the_sensor_id_and_a_station_id__the_station_does_not_have_the_sensor__throws_ModuleNotFoundException()
    {
        $expected_result = 'Resource not found';
        $station_id = strval(StationsTableSeeder::$PUBLIC_STATION_ID);
        $sensor_id = strval(SensorsTableSeeder::$SENSOR_FROM_PRIVATE_STATION_ID);

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors/' . $sensor_id . '/readings/latest-values');

        $json = $response->json();
        self::assertEquals($json['error'], $expected_result);
    }

    public function test__Get_sensor_latest_values_from_the_sensor_id_and_a_public_station_id__sensor_exists__returns_a_success()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;

        $response = $this->get(SensorsAPITest::URL . '/' . $station_id . '/sensors/' . $sensor_id . '/readings/latest-values');

        $response->assertStatus(200);
    }

    public function test__Get_sensor_latest_values_from_the_sensor_id_and_the_its_station_id__returns_latest_values_with_the_correct_format()
    {
        $expected_station_id = strval(StationsTableSeeder::$PUBLIC_STATION_ID);
        $sensor_id = strval(SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID);
        $expected_sensor_format = [
            'pm2.5' => [],
            'pm10' => []
        ];

        $response = $this->get(SensorsAPITest::URL . '/' . $expected_station_id . '/sensors/' . $sensor_id . '/readings/latest-values');

        $json = $response->json();
        $actual_sensor_format = $json;
        $key_differences = array_diff_key($expected_sensor_format, $actual_sensor_format);
        self::assertEquals(0, count($key_differences));
    }

}
