<?php


namespace Tests\Unit;
use App\Exceptions\ForbiddenAccessException;
use App\History;
use App\Http\Controllers\HistoryController;
use App\Http\Repositories\HistoryRepository;
use App\Http\Repositories\SensorReadingRepository;
use App\Http\Repositories\SensorRepository;
use App\Http\Repositories\StationRepository;
use App\Http\Requests\SensorReadingRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use ReadingTypesTableSeeder;
use SensorsTableSeeder;
use StationsTableSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HistoryControllerTest extends TestCase
{
    use DatabaseTransactions;

    const FIND_WHERE_METHOD = 'findWhere';
    const FIND_BY_FIELD_METHOD = 'findByField';
    const GET_READING_TYPES_METHOD = 'getReadingTypes';
    const GENERATE_HISTORY_METHOD = 'generateHistory';
    const COLLECTION_WITH_RESULTS = [0,1,2,3,4,5];
    const COLLECTION_WITH_RESULT = [1];
    const COLLECTION_WITH_NO_RESULT = [];

    private $stationRepositoryMock;
    private $sensorRepositoryMock;
    private $sensorReadingRepositoryMock;
    private $historyRepositoryMock;
    private $sensorReadingRequestMock;
    private $historyController;
    private $history;

    public function setUp()
    {
        $this->stationRepositoryMock = Mockery::Mock(StationRepository::class);
        $this->sensorRepositoryMock = Mockery::Mock(SensorRepository::class);
        $this->sensorReadingRepositoryMock = Mockery::Mock(SensorReadingRepository::class);
        $this->sensorReadingRequestMock = Mockery::Mock(SensorReadingRequest::class);
        $this->historyRepositoryMock = Mockery::Mock(HistoryRepository::class);
        $this->history = new History([
            "lastHourReadings" => [1,2],
            "lastDayReadings" => [1,2,3,4],
            "lastWeekReadings" => [1,2,3,4,5,6]
        ]);

        $this->historyController = new HistoryController
        (
            $this->stationRepositoryMock,
            $this->sensorRepositoryMock,
            $this->sensorReadingRepositoryMock,
            $this->historyRepositoryMock
        );
    }

    public function test__showHistory__station_id_sensor_id_and_reading_type_are_valid__returns_a_HistoryResource()
    {
        $expectedResource = 'App\Http\Resources\HistoryResource';
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $type = ReadingTypesTableSeeder::$TYPE_PM2P5;
        $this->stationRepositoryMock->shouldReceive(HistoryControllerTest::FIND_WHERE_METHOD)->andReturn(HistoryControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepositoryMock->shouldReceive(HistoryControllerTest::FIND_WHERE_METHOD)->andReturn(HistoryControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorReadingRepositoryMock->shouldReceive(HistoryControllerTest::FIND_WHERE_METHOD)->andReturn(HistoryControllerTest::COLLECTION_WITH_RESULTS);
        $this->historyRepositoryMock->shouldReceive(HistoryControllerTest::GENERATE_HISTORY_METHOD)->andReturn($this->history);

        $resource = $this->historyController->showHistory($station_id, $sensor_id, $type);

        $this->assertEquals($expectedResource, get_class($resource));
    }

    public function test__showHistory__no_sensor_founded__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;
        $type = ReadingTypesTableSeeder::$TYPE_HUMIDITY;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);

        $this->historyController->showHistory($station_id, $sensor_id, $type);
    }

    public function test__showHistory__no_public_station_founded__throws_ForbiddenAccessException()
    {
        $this->expectException(ForbiddenAccessException::class);
        $station_id = StationsTableSeeder::$NON_EXISTING_STATION_ID;
        $sensor_id = SensorsTableSeeder::$SENSOR_FROM_PUBLIC_STATION_ID;
        $type = ReadingTypesTableSeeder::$TYPE_HUMIDITY;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);

        $this->historyController->showHistory($station_id, $sensor_id, $type);
    }

    public function test__showHistory__no_sensor_reading_founded__throws_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $station_id = StationsTableSeeder::$PUBLIC_STATION_ID;
        $sensor_id = SensorsTableSeeder::$NON_EXISTING_SENSOR_ID;
        $type = ReadingTypesTableSeeder::$TYPE_HUMIDITY;
        $this->stationRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_RESULTS);
        $this->sensorReadingRepositoryMock->shouldReceive(SensorReadingControllerTest::FIND_WHERE_METHOD)->andReturn(SensorReadingControllerTest::COLLECTION_WITH_NO_RESULT);

        $this->historyController->showHistory($station_id, $sensor_id, $type);
    }
}