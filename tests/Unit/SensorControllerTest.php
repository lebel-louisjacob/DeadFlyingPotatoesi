<?php
/**
 * Created by PhpStorm.
 * User: whatever
 * Date: 2/15/18
 * Time: 10:31 AM
 */

namespace Tests\Unit;
use App\Exceptions\ForbiddenAccessException;
use App\Http\Controllers\SensorController;
use App\Http\Repositories\SensorRepository;
use App\Http\Repositories\StationRepository;
use App\Http\Resources\SensorResource;
use App\Sensor;
use App\Station;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use SensorsTableSeeder;
use StationsTableSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SensorControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    const FIND_WHERE_METHOD = 'findWhere';
    const FIND_BY_FIELD_METHOD = 'findByField';
    const COLLECTION_WITH_RESULTS = ['id' => 0];
    const COLLECTION_WITH_NO_RESULT = [];

    private $fakeReadingsPM2p5 = [];
    private $fakeReadingsPM10 = [];

    public function setUp()
    {
        $this->stationRepository = Mockery::Mock(StationRepository::class);
        $this->sensorRepository = Mockery::Mock(SensorRepository::class);
        $this->sensorResource = ["items"];
        $this->sensorResource["items"] = new SensorResource([
            'id' => SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID,
            'info' => [
                'name' => "nom",
                'station_id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'types' => ["Croissant"],
                'range' => 1.12
            ]]);

        $this->sensorController = new SensorController
        (
            $this->stationRepository,
            $this->sensorRepository
        );
    }

    public function test__showAllPublicSensors__station_id_is_valid__returns_a_collection_of_SensorReadingResource()
    {
        $expectedCollection = 'App\Http\Resources\SensorResource';
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_BY_FIELD_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);

        $resource = $this->sensorController->showAllPublicSensors($station_id);

        $this->assertEquals($expectedCollection, $resource->collects);


    }

    public function test__showAllPublicSensors__no_sensor_founded__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorControllerTest::COLLECTION_WITH_NO_RESULT);

        $this->sensorController->showAllPublicSensors($station_id);
    }

    public function test__showAllPublicSensors__no_public_station_founded__throws_ForbiddenAccessException()
    {
        $this->expectException(ForbiddenAccessException::class);

        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with([
                'id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'is_private' => 0
            ])
            ->andReturn([]);
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with(['id' => StationsTableSeeder::$PUBLIC_STATION_ID])
            ->andReturn([new Station()]);


        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorControllerTest::COLLECTION_WITH_NO_RESULT);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorControllerTest::COLLECTION_WITH_RESULTS);

        $this->sensorController->showAllPublicSensors($station_id);
    }

    public function test__showPublicSensor__station_id_is_valid__returns_a_collection_of_SensorReadingResource()
    {
        $expectedCollection = 'App\Http\Resources\SensorResource';
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn($this->sensorResource);

        $resource = $this->sensorController->showPublicSensor($station_id, $sensor_id);

        $this->assertEquals($expectedCollection, get_class($resource));

    }

    public function test__showPublicSensor__no_sensor_founded__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with([
                'id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'is_private' => 0
            ])
            ->andReturn([]);
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with(['id' => StationsTableSeeder::$PUBLIC_STATION_ID])
            ->andReturn([]);


        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorControllerTest::COLLECTION_WITH_NO_RESULT);

        $this->sensorController->showPublicSensor($station_id, $sensor_id);
    }

    public function test__showPublicSensor__no_public_station_founded__throws_ForbiddenAccessException()
    {
        $this->expectException(ForbiddenAccessException::class);

        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with([
                'id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'is_private' => 0
            ])
            ->andReturn([]);
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with(['id' => StationsTableSeeder::$PUBLIC_STATION_ID])
            ->andReturn([new Station()]);

        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorControllerTest::COLLECTION_WITH_NO_RESULT);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn(SensorControllerTest::COLLECTION_WITH_RESULTS);

        $this->sensorController->showPublicSensor($station_id, $sensor_id);
    }

    public function test_showLatestValues_method_call_stationRepository_with_good_parameter()
    {
        //arrange
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn([new Station()]);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn([new Sensor()]);
        $this->sensorRepository->shouldReceive('getReadingTypes')->andReturn(["pm10"]);
        $this->sensorRepository->shouldReceive('getReadings')->andReturn(collect(SensorControllerTest::COLLECTION_WITH_RESULTS));

        //act
        $this->sensorController->showLatestValues(
            StationsTableSeeder::$PUBLIC_STATION_ID,
            SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID);

        //assert
        $this->stationRepository->shouldHaveReceived('findWhere')
            ->with([
                'id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'is_private' => 0
            ]);
    }

    public function test_showLatestValues_method_throw_ForbiddenAccessException_if_station_is_private()
    {
        //arrange
        $this->expectException(ForbiddenAccessException::class);

        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with([
                'id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'is_private' => 0
            ])
            ->andReturn([]);
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with(['id' => StationsTableSeeder::$PUBLIC_STATION_ID])
            ->andReturn([new Station()]);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn([new Sensor()]);
        $this->sensorRepository->shouldReceive('getReadingTypes')->andReturn(["pm10"]);
        $this->sensorRepository->shouldReceive('getReadings')->andReturn(collect(SensorControllerTest::COLLECTION_WITH_RESULTS));

        //act
        $this->sensorController->showLatestValues(
            StationsTableSeeder::$PUBLIC_STATION_ID,
            SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID);
    }

    public function test_showLatestValues_method_throw_notFound_if_station_doesnt_exist()
    {
        //arrange
        $this->expectException(ModelNotFoundException::class);

        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with([
                'id' => StationsTableSeeder::$PUBLIC_STATION_ID,
                'is_private' => 0
            ])
            ->andReturn([]);
        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)
            ->with(['id' => StationsTableSeeder::$PUBLIC_STATION_ID])
            ->andReturn([]);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn([new Sensor()]);
        $this->sensorRepository->shouldReceive('getReadingTypes')->andReturn(["pm10"]);
        $this->sensorRepository->shouldReceive('getReadings')->andReturn(collect(SensorControllerTest::COLLECTION_WITH_RESULTS));

        //act
        $this->sensorController->showLatestValues(
            StationsTableSeeder::$PUBLIC_STATION_ID,
            SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID);
    }

    public function test_showLatestValues_returns_table_of_latest_values_of_each_pollutant()
    {
        // Arrange
        $EXPECTED_TABLE = [];

        $EXPECTED_TABLE["pm2.5"] = $this->fakeReadingsPM2p5;
        $EXPECTED_TABLE["pm10"] = $this->fakeReadingsPM10;

        $this->stationRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn([new Station()]);
        $this->sensorRepository->shouldReceive(SensorControllerTest::FIND_WHERE_METHOD)->andReturn([new Sensor()]);
        $this->sensorRepository->shouldReceive('getReadingTypes')->andReturn(["pm2.5", "pm10"]);
        $this->sensorRepository->shouldReceive('getReadings')->andReturn(collect(SensorControllerTest::COLLECTION_WITH_RESULTS));

        foreach($EXPECTED_TABLE as $key => $pollutant){
            for($count = 48; $count > 0; $count--){
                $EXPECTED_TABLE[$key][$count] = 0;
            }
        }

        // Act
        $actual = $this->sensorController->showLatestValues(
            StationsTableSeeder::$PUBLIC_STATION_ID,
            SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID
        );

        // Assert
        $this->assertEquals($EXPECTED_TABLE, $actual);
    }

}