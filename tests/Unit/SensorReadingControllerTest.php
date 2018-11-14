<?php


namespace Tests\Unit;
use App\Auth\LoginProxy;
use App\Exceptions\ForbiddenAccessException;
use App\Http\Controllers\SensorReadingController;
use App\Http\Repositories\SensorReadingRepository;
use App\Http\Repositories\SensorRepository;
use App\Http\Repositories\StationRepository;
use App\Http\Requests\SensorReadingRequest;
use App\Sensor;
use App\SensorReading;
use App\Users\Admin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use ReadingTypesTableSeeder;
use SensorReadingsTableSeeder;
use SensorsTableSeeder;
use StationsTableSeeder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SensorReadingControllerTest extends TestCase
{
    use DatabaseTransactions;

    const FIND_WHERE_METHOD = 'findWhere';
    const FIND_BY_FIELD_METHOD = 'findByField';
    const GET_READING_TYPES_METHOD = 'getReadingTypes';
    const COLLECTION_WITH_RESULTS = [0,1,2,3,4,5];
    const COLLECTION_WITH_RESULT = [1];
    const COLLECTION_WITH_NO_RESULT = [];
    const VALID_SENSOR_READING_VALUE = 5;
    const VALID_SENSOR_READING_LATITUDE = 4.2;
    const VALID_SENSOR_READING_LONGITUDE = 4.2;
    const INVALID_SENSOR_ID = 3;
    const VALID_SENSOR_ID = 2;

    private $stationRepositoryMock;
    private $sensorRepositoryMock;
    private $sensorReadingRepositoryMock;
    private $sensorReadingRequestMock;
    private $sensorReadingController;
    private $loginControllerSpy;
    private $sensorReading;

    public function setUp()
    {
        $this->stationRepositoryMock = Mockery::Mock(StationRepository::class);
        $this->sensorRepositoryMock = Mockery::Mock(SensorRepository::class);
        $this->sensorReadingRepositoryMock = Mockery::Mock(SensorReadingRepository::class);
        $this->sensorReadingRequestMock = Mockery::Mock(SensorReadingRequest::class);
        $this->loginControllerSpy = Mockery::Spy(LoginProxy::class);
        $this->sensor = new Sensor([
            "id" => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID,
            "model_id" => 1,
            "station_id" => StationsTableSeeder::$PUBLIC_STATION_ID
        ]);
        $this->sensorReading = new SensorReading([
            'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
            'sensor_id' => 1,
            'latitude' => StationsTableSeeder::$STATION_1_LAT,
            'longitude' => StationsTableSeeder::$STATION_1_LON,
            'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        $this->sensorReadingController = new SensorReadingController
        (
            $this->stationRepositoryMock,
            $this->sensorRepositoryMock,
            $this->sensorReadingRepositoryMock
        );
    }

    public function test__showAllPublicSensorReadings__station_id_and_sensor_id_are_valid__returns_a_collection_of_SensorReadingResource()
    {
        $expectedCollection = 'App\Http\Resources\SensorReadingResource';
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_BY_FIELD_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);

        $resource = $this->sensorReadingController->showAllPublicSensorReadings($station_id, $sensor_id);

        $this->assertEquals($expectedCollection, $resource->collects);
    }

    public function test__showAllPublicSensorReadings__no_sensor_founded__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_BY_FIELD_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);

        $this->sensorReadingController->showAllPublicSensorReadings($station_id, $sensor_id);
    }

    public function test__showAllPublicSensorReadings__no_public_station_founded__throws_ForbiddenAccessException()
    {
        $this->expectException(ForbiddenAccessException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_BY_FIELD_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);

        $this->sensorReadingController->showAllPublicSensorReadings($station_id, $sensor_id);
    }

    public function test__showAllPublicSensorReadings__no_sensor_reading_founded__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_BY_FIELD_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);

        $this->sensorReadingController->showAllPublicSensorReadings($station_id, $sensor_id);
    }

    public function test__showPublicReadingsByType__station_id_sensor_id_and_type_are_valid__returns_a_collection_of_SensorReadingResource()
    {
        $expectedCollection = 'App\Http\Resources\SensorReadingResource';
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $type = ReadingTypesTableSeeder::$TYPE_HUMIDITY;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);

        $resource = $this->sensorReadingController->showPublicReadingsByType($station_id, $sensor_id, $type);

        $this->assertEquals($expectedCollection, $resource->collects);
    }

    public function test__showPublicReadingsByType__no_sensor_founded__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;
        $type = ReadingTypesTableSeeder::$TYPE_HUMIDITY;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);

        $this->sensorReadingController->showPublicReadingsByType($station_id, $sensor_id, $type);
    }

    public function test__showPublicReadingsByType__no_public_station_founded__throws_ForbiddenAccessException()
    {
        $this->expectException(ForbiddenAccessException::class);
        $station_id = StationsTableSeeder::$NON_EXISTING_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $type = ReadingTypesTableSeeder::$TYPE_HUMIDITY;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);

        $this->sensorReadingController->showPublicReadingsByType($station_id, $sensor_id, $type);
    }

    public function test__showPublicReadingsByType__no_sensor_reading_founded__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;
        $type = ReadingTypesTableSeeder::$TYPE_HUMIDITY;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);

        $this->sensorReadingController->showPublicReadingsByType($station_id, $sensor_id, $type);
    }

    public function test__store__station_does_not_exist__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $station_id = StationsTableSeeder::$NON_EXISTING_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULT);

        $this->sensorReadingController->store($station_id, $sensor_id, $this->sensorReadingRequestMock);
    }

    public function test__store__sensor_is_not_in_the_station__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULT);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);

        $this->sensorReadingController->store($station_id, $sensor_id, $this->sensorReadingRequestMock);
    }

    public function test__store__request_type_does_not_match_with_sensor_model_types__throws_BadRequestHttpException()
    {
        $this->expectException(BadRequestHttpException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;
        $request = new SensorReadingRequest([
            'id' => 1,
            'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
            'latitude' => StationsTableSeeder::$STATION_1_LAT,
            'longitude' => StationsTableSeeder::$STATION_1_LON,
            'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULT);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(collect($this->sensor));
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::GET_READING_TYPES_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);

        $this->sensorReadingController->store($station_id, $sensor_id, $request);
    }

    public function test__store__station_sensor_and_request_are_valid__create_an_object()
    {
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $expected_request = new SensorReadingRequest([
            'id' => 1,
            'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
            'latitude' => StationsTableSeeder::$STATION_1_LAT,
            'longitude' => StationsTableSeeder::$STATION_1_LON,
            'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
            'sensor_id' => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID
        ]);
        $request = new SensorReadingRequest([
            'id' => 1,
            'value' => SensorReadingsTableSeeder::$SENSOR_READING_2_VALUE,
            'latitude' => StationsTableSeeder::$STATION_1_LAT,
            'longitude' => StationsTableSeeder::$STATION_1_LON,
            'type' => ReadingTypesTableSeeder::$TYPE_PM2P5,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULT);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(collect($this->sensor));
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::GET_READING_TYPES_METHOD)->andReturn([ReadingTypesTableSeeder::$TYPE_PM2P5]);
        $this->sensorReadingRepositoryMock->shouldReceive('create')->andReturn($this->sensorReading);

        $this->sensorReadingController->store($station_id, $sensor_id, $request);

        $this->sensorReadingRepositoryMock->shouldHaveReceived('create')
            ->with($expected_request->all())
            ->once();
    }
}