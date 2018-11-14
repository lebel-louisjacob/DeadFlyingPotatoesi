<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use SensorsTableSeeder;
use Tests\TestCase;
use StationsTableSeeder;
use SensorReadingsTableSeeder;
use App\Station;
use UsersTableSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StationApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    const URL = '/api/stations';

    public function setUp()
    {
        parent::setUp();
        $this->artisan('passport:client', ['--password' => null, '--no-interaction' => true]);
    }

    public function test__Get_stations__returns_a_success()
    {
        $response = $this->get(StationApiTest::URL);

        $response->assertStatus(200);
    }

    public function test__Get_station__returns_station_with_the_correct_format()
    {
        $expected_number_of_differences = 0;
        $expected_station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $expected_station_format = array([
            'id' => $expected_station_id,
            'is_private' => 0,
            'aqi' => [
                'aqi' => 1,
                'average' => 2,
                'color' => 'null',
                'label' => 'null',
                'maximum' => 3,
            ],
            'info' => [
                'city' => 'cafe',
                'latitude' => 4.2,
                'longitude' => 6.2,
                'name' => 'Station test 1'
            ]
        ]);

        $response = $this->get(StationApiTest::URL);

        $json = $response->json();
        $actual_station_format = $json;
        $key_differences = array_diff_key($expected_station_format, $actual_station_format);
        self::assertEquals($expected_number_of_differences, count($key_differences));
    }

    public function test__Get_station_with_id__station_is_public__returns_a_success()
    {
        $station_id = strval(StationsTableSeeder::$PUBLIC_STATION_ID);

        $response = $this->get(StationApiTest::URL. '/' .$station_id);

        $response->assertStatus(200);
    }

    public function test__Get_station_with_id__station_is_private__throws_forbidden_access()
    {
        $station_id = strval(StationsTableSeeder::$PRIVATE_STATION_ID);

        $response = $this->get(StationApiTest::URL. '/' .$station_id);

        $response->assertStatus(403);
    }

    public function test__Get_station_with_id__returns_station_with_the_correct_format()
    {
        $expected_number_of_differences = 0;
        $expected_station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $expected_station_format = [
            'id' => $expected_station_id,
            'is_private' => 0,
            'aqi' => [
                'aqi' => 1,
                'average' => 2,
                'color' => 'null',
                'label' => 'null',
                'maximum' => 3,
            ],
            'info' => [
                'city' => 'cafe',
                'latitude' => 4.2,
                'longitude' => 6.2,
                'name' => 'Station test 1'
            ]];


        $response = $this->get(StationApiTest::URL . '/' .$expected_station_id);

        $actual_station_format = $response->json();

        $key_differences = array_diff_key($expected_station_format, $actual_station_format);
        self::assertEquals($expected_number_of_differences, count($key_differences));
    }

    public function test__Get_station_with_id__station_is_public__returns_station_with_the_correct_id()
    {
        $expected_station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $response = $this->get(StationApiTest::URL . '/' .$expected_station_id);

        $actual_station = $response->json();
        $actual_station_id = $actual_station['id'];
        self::assertEquals($expected_station_id, $actual_station_id);
    }

    public function test__Get_station_with_id_and_station_does_not_exist__throws_not_found()
    {
        $expected_result = "Resource not found";
        $station_id = strval(StationsTableSeeder::$NON_EXISTING_STATION_ID);

        $response = $this->get(StationApiTest::URL . '/' .$station_id);

        $json = $response->json();
        self::assertEquals($json['error'], $expected_result);
    }

    public function test__Get_station_with_invalid_id__throws_not_found()
    {
        $station_id = strval(StationsTableSeeder::$NON_EXISTING_STATION_ID);

        $response = $this->get(StationApiTest::URL . '/' . $station_id);

        $response->assertStatus(404);
    }

    public function test_get_station_by_id_return_only_one_station()
    {
        $STATION_ID = 20;
        factory(Station::class)->create([
            'id' => $STATION_ID,
            'name' => 'Test',
            'city' => 'Montreal',
            'latitude' => 50,
            'longitude' => 50,
            'is_private' => false
        ]);
        $response = $this->json('GET', '/api/stations/'.$STATION_ID);

        $response->assertJson([
            'id' => $STATION_ID
        ]);
    }

    public function test_post_station_with_token_admin_scope_return_success()
    {
        $response = $this->json('POST', '/api/login',
            ['email' => UsersTableSeeder::ADMIN_EMAIL, 'password' => UsersTableSeeder::PASSWORD]);
        $token = json_decode($response->content())->access_token;

        $post_response = $this->json('POST', '/api/stations',
            ['name' => 'Test', 'city' => 'Montréal', 'latitude' => 50, 'longitude' => 50, 'is_private' => 0],
            ['HTTP_Authorization' => 'Bearer ' . $token]);

        $post_response->assertSuccessful();
    }

    public function test_post_station_with_token_admin_scope_return_success_and_station_is_accessible_with_get()
    {
        // Arrange
        $response = $this->json('POST', '/api/login',
            ['email' => UsersTableSeeder::ADMIN_EMAIL, 'password' => UsersTableSeeder::PASSWORD]);
        $token = json_decode($response->content())->access_token;

        // Act
        $post_response = $this->json('POST', '/api/stations',
            ['name' => 'Test', 'city' => 'Montreal', 'latitude' => 50, 'longitude' => 50, 'is_private' => 0],
            ['HTTP_Authorization' => 'Bearer ' . $token]);

        $createdStationId = $post_response->decodeResponseJson()['id'];

        $get_response = $this->json('GET', '/api/stations/'.$createdStationId);

        // Assert
        $get_response->assertSuccessful();
        $get_response->assertJson(['id' => $createdStationId,]);
        $get_response->assertJson(['info' => ['name' => 'Test']]);
    }

    public function test_put_station_with_token_admin_scope_update_the_model()
    {
        $STATION_ID = 1;
        $EXPECTED_NAME = 'Test1';
        $response = $this->json('POST', '/api/login',
            ['email' => UsersTableSeeder::ADMIN_EMAIL, 'password' => UsersTableSeeder::PASSWORD]);
        $token = json_decode($response->content())->access_token;

        $put_response = $this->json('PUT', '/api/stations/'.$STATION_ID,
            ['name' => $EXPECTED_NAME, 'city' => 'Montréal', 'latitude' => 50, 'longitude' => 50, 'is_private' => 0],
            ['HTTP_Authorization' => 'Bearer ' . $token]);

        $put_response->assertJson([
            'name' => $EXPECTED_NAME,
        ]);
    }

    public function test__Get_station_latest_aqi_from_the_station_id__the_station_id_does_not_exist__throws_ModuleNotFoundException()
    {
        $expected_result = 'Resource not found';
        $station_id = StationsTableSeeder::$NON_EXISTING_STATION_ID;

        $response = $this->get(StationApiTest::URL . '/' . $station_id . '/sensors/latest-aqi');

        $json = $response->json();
        self::assertEquals($json['error'], $expected_result);
    }

    public function test__Get_station_latest_aqi_from_the_sensor_id_and_a_station_id__the_station_id_does_not_exist__returns_code_404()
    {
        $station_id = StationsTableSeeder::$NON_EXISTING_STATION_ID;

        $response = $this->get(StationApiTest::URL . '/' . $station_id . '/sensors/latest-aqi');

        $response->assertStatus(404);
    }

    public function test__Get_station_latest_aqi_from_the_public_station_id__sensor_exists__returns_a_success()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;

        $response = $this->get(StationApiTest::URL . '/' . $station_id . '/sensors/latest-aqi');

        $response->assertStatus(200);
    }

    public function test__Get_station_latest_aqi_from_the_station_id__returns_aqi_with_the_correct_format()
    {
        $expected_station_id = strval(StationsTableSeeder::$PUBLIC_STATION_ID);
        $sensor_id = strval(SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID);
        $expected_sensor_format = [
            'pm2.5' => [],
            'pm10' => []
        ];

        $response = $this->get(StationApiTest::URL . '/' . $expected_station_id . '/sensors/' . $sensor_id . '/readings/latest-values');

        $json = $response->json();
        $actual_sensor_format = $json;
        $key_differences = array_diff_key($expected_sensor_format, $actual_sensor_format);
        self::assertEquals(0, count($key_differences));
    }
}