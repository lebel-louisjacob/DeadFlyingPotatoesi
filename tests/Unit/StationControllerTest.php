<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-15
 * Time: 9:50 AM
 */

namespace Tests\Unit;

use App\AQI\AQIRating;
use App\AQI\QuebecNormForAQIpm10;
use App\AQI\QuebecNormForAQIpm2P5;
use App\Exceptions\ForbiddenAccessException;
use App\Http\Controllers\StationController;
use App\Http\Repositories\StationRepository;
use App\Http\Requests\StationRequest;
use App\Station;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StationControllerTest extends TestCase
{
    use DatabaseTransactions;

    const STATION_ID = 1;
    const STATION_NAME = 'StationName';
    const STATION_CITY = 'StationCity';
    const STATION_LATITUDE = 10;
    const STATION_LONGITUDE = 90;
    const STATION_PUBLIC = 0;
    const STATION_PRIVATE = 1;
    const AQI_HOURS_AGO = 4;
    const AQI_INTERVAL = 4;
    const LATEST_AQI_HOURS_AGO = 48;
    const LATEST_AQI_INTERVAL = 1;

    private $stationRepositoryMock;
    private $stationController;
    private $station;
    private $fakeReadingsPM2p5;
    private $fakeReadingsPM10;
    private $fakeTemperature;
    private $fakeHumidity;
    private $pm2p5Calculation;
    private $pm10Calculation;

    public function setUp()
    {
        parent::setUp();

        // Calculation objects

        $this->pm2p5Calculation = new QuebecNormForAQIpm2P5();
        $this->pm10Calculation = new QuebecNormForAQIpm10();

        $this->station = new Station([
            'id' => 1,
            'name' => self::STATION_NAME,
            'city' => self::STATION_CITY,
            'latitude' => self::STATION_LATITUDE,
            'longitude' => self::STATION_LONGITUDE,
            'is_private' => self::STATION_PUBLIC
        ]);

        // Fake readings
        $this->fakeReadingsPM2p5 = collect([]);
        $this->fakeReadingsPM10 = collect([]);
        $this->fakeTemperature = collect([]);
        $this->fakeHumidity = collect([]);

        $this->stationRepositoryMock = Mockery::mock(StationRepository::class);
        $this->stationRepositoryMock->shouldReceive('create')->andReturn($this->station);
        $this->stationRepositoryMock->shouldReceive('update')->andReturn($this->station);

        $this->stationRepositoryMock->shouldReceive("findByField")->andReturn(collect([$this->station]));
        $this->stationRepositoryMock->shouldReceive("getReadings")->withArgs([
            $this->station->id,
            'Humidity',
            self::AQI_HOURS_AGO,
            self::AQI_INTERVAL])
            ->andReturn($this->fakeHumidity);

        $this->stationRepositoryMock->shouldReceive("getReadings")
            ->withArgs([
                $this->station->id,
                'Temperature',
                self::AQI_HOURS_AGO,
                self::AQI_INTERVAL
            ])
            ->andReturn($this->fakeTemperature);

        $this->stationRepositoryMock->shouldReceive("getReadings")
        ->withArgs([
            $this->station->id,
            'pm2.5',
            self::AQI_HOURS_AGO,
            self::AQI_INTERVAL
        ])
        ->andReturn($this->fakeReadingsPM2p5);

        $this->stationRepositoryMock->shouldReceive("getReadings")
            ->withArgs([
                $this->station->id,
                'pm10',
                self::AQI_HOURS_AGO,
                self::AQI_INTERVAL
            ])
            ->andReturn($this->fakeReadingsPM10);

        $this->stationController = new StationController($this->stationRepositoryMock);
    }

    public function test_store_method_call_stationRepository_once_with_good_request()
    {
        //arrange
        $request = new StationRequest([
            'id' => self::STATION_ID,
            'name' => self::STATION_NAME,
            'city' => self::STATION_CITY,
            'latitude' => self::STATION_LATITUDE,
            'longitude' => self::STATION_LONGITUDE,
            'is_private' => self::STATION_PUBLIC
            ]);

        //act
        $this->stationController->store($request);

        //assert
        $this->stationRepositoryMock->shouldHaveReceived('create')
            ->with($request->all())
            ->once();
    }

    public function test_showAllPublicStations_method_call_stationRepository_once_with_good_parameter()
    {
        //arrange

        //expect
        $this->stationRepositoryMock->shouldReceive('findByField')->andReturn(collect($this->station));

        //act
        $this->stationController->showAllPublicStations();

        //assert
        $this->stationRepositoryMock->shouldHaveReceived('findByField')
            ->with('is_private', self::STATION_PUBLIC)
            ->once();
    }

    public function test_showPublicStation_method_call_stationRepository_once_with_good_parameter()
    {
        //arrange

        //expect
        $this->stationRepositoryMock->shouldReceive('findByField')->andReturn(collect([$this->station]));

        //act
        $this->stationController->showPublicStation(self::STATION_ID);

        //assert
        $this->stationRepositoryMock->shouldHaveReceived('findByField')
            ->with('id', self::STATION_ID);
    }

    public function test_showPublicStation_method_throw_ForbiddenAccessException_if_station_is_private()
    {
        //arrange
        $this->expectException(ForbiddenAccessException::class);

        $this->station->is_private = self::STATION_PRIVATE;
        //expect
        //$this->stationRepositoryMock->shouldReceive('findByField')->andReturn(collect([$private_station]));

        //act
        $test = $this->stationController->showPublicStation(self::STATION_ID);

        //assert
    }

    public function test_update_method_call_stationRepository_once_with_good_request_and_stationId()
    {
        //arrange
        $request = new StationRequest([
            'id' => self::STATION_ID,
            'name' => self::STATION_NAME,
            'city' => self::STATION_CITY,
            'latitude' => self::STATION_LATITUDE,
            'longitude' => self::STATION_LONGITUDE,
            'is_private' => self::STATION_PUBLIC
        ]);

        //act
        $this->stationController->update($request, self::STATION_ID);

        //assert
        $this->stationRepositoryMock->shouldHaveReceived('update')
            ->with($request->all(), self::STATION_ID)
            ->once();
    }

    public function test_destroy_method_call_stationRepository_once_with_good_stationId()
    {
        //arrange

        //expect
        $this->stationRepositoryMock->shouldReceive('delete')->andReturn();

        //act
        $this->stationController->destroy(self::STATION_ID);

        //assert
        $this->stationRepositoryMock->shouldHaveReceived('delete')
            ->with(self::STATION_ID)
            ->once();
    }


    public function test_AQI_no_readings_gives_0()
    {
        // Arrange
        $EXPECTED_AVERAGE_AQI = 0;

        // Act
        $this->stationController->showPublicStation(1);

        // Assert
        $this->assertEquals($EXPECTED_AVERAGE_AQI, $this->station->aqiAverage);
    }

    public function test_AQI_gives_worst_average()
    {
        // Arrange
        $EXPECTED_AVERAGE_AQI = $this->pm2p5Calculation->calculateAQI(35);

        $this->fakeReadingsPM2p5->push(['value' => 30]);
        $this->fakeReadingsPM2p5->push(['value' => 40]);

        $this->fakeReadingsPM10->push(['value' => 20]);
        $this->fakeReadingsPM10->push(['value' => 30]);

        // Act
        $this->stationController->showPublicStation(1);

        // Assert
        $this->assertEquals($EXPECTED_AVERAGE_AQI, $this->station->aqiAverage);
    }

    public function test_AQI_gives_worst_maximum()
    {
        // Arrange
        $EXPECTED_MAXIMUM_AQI = $this->pm10Calculation->calculateAQI(40);

        $this->fakeReadingsPM2p5->push(['value' => 20]);
        $this->fakeReadingsPM2p5->push(['value' => 39]);

        $this->fakeReadingsPM10->push(['value' => 30]);
        $this->fakeReadingsPM10->push(['value' => 40]);

        // Act
        $this->stationController->showPublicStation(1);

        // Assert
        $this->assertEquals($EXPECTED_MAXIMUM_AQI, $this->station->aqiMax);
    }

    public function test_AQI_gives_correct_label()
    {
        // Arrange
        $EXPECTED_AVERAGE = $this->pm10Calculation->calculateAQI(35);
        $EXPECTED_RATING = AQIRating::getRating($EXPECTED_AVERAGE);
        $EXPECTED_RATING_LABEL = $EXPECTED_RATING['label'];

        $this->fakeReadingsPM2p5->push(['value' => 20]);
        $this->fakeReadingsPM2p5->push(['value' => 30]);

        $this->fakeReadingsPM10->push(['value' => 30]);
        $this->fakeReadingsPM10->push(['value' => 40]);

        // Act
        $this->stationController->showPublicStation(1);

        // Assert
        $this->assertEquals($EXPECTED_RATING_LABEL, $this->station->aqiLabel);
    }

    public function test_AQI_gives_correct_color()
    {
        // Arrange
        $EXPECTED_AVERAGE = $this->pm10Calculation->calculateAQI(35);
        $EXPECTED_RATING = AQIRating::getRating($EXPECTED_AVERAGE);
        $EXPECTED_RATING_COLOR = $EXPECTED_RATING['color'];

        $this->fakeReadingsPM2p5->push(['value' => 20]);
        $this->fakeReadingsPM2p5->push(['value' => 30]);

        $this->fakeReadingsPM10->push(['value' => 30]);
        $this->fakeReadingsPM10->push(['value' => 40]);

        // Act
        $this->stationController->showPublicStation(1);

        // Assert
        $this->assertEquals($EXPECTED_RATING_COLOR, $this->station->aqiColor);
    }

    public function test_humidity_give_correct_humidity(){
        //Arrange
        $EXCEPTED_HUMIDITY = 35;
        $this->fakeHumidity->push(['value' => 30]);
        $this->fakeHumidity->push(['value' => 40]);

        //Act
        $this->stationController->showPublicStation(1);

        //Assert
        $this->assertEquals($EXCEPTED_HUMIDITY, $this->station->aqiHumidity);

    }

    public function test_temperature_give_correct_temperature(){
        //Arrange
        $EXCEPTED_TEMPERATURE = 35;
        $this->fakeTemperature->push(['value' => 30]);
        $this->fakeTemperature->push(['value' => 40]);

        //Act
        $this->stationController->showPublicStation(1);

        //Assert
        $this->assertEquals($EXCEPTED_TEMPERATURE, $this->station->aqiTemperature);

    }

    public function test_aqi_pm2p5_give_correct_pm2p5(){
        //Arrange
        $EXPECTED_PM2P5 = $this->pm2p5Calculation->calculateAQI(25);

        $this->fakeReadingsPM2p5->push(['value' => 20]);
        $this->fakeReadingsPM2p5->push(['value' => 30]);

        $this->fakeReadingsPM10->push(['value' => 30]);
        $this->fakeReadingsPM10->push(['value' => 40]);

        //Act
        $this->stationController->showPublicStation(1);

        //Assert
        $this->assertEquals($EXPECTED_PM2P5, $this->station->pm2p5);

    }

    public function test_aqi_pm10_give_correct_pm10(){
        //Arrange
        $EXPECTED_PM10 = $this->pm10Calculation->calculateAQI(35);

        $this->fakeReadingsPM2p5->push(['value' => 20]);
        $this->fakeReadingsPM2p5->push(['value' => 30]);

        $this->fakeReadingsPM10->push(['value' => 30]);
        $this->fakeReadingsPM10->push(['value' => 40]);

        //Act
        $this->stationController->showPublicStation(1);

        //Assert
        $this->assertEquals($EXPECTED_PM10, $this->station->pm10);

    }

    public function test_raw_data_pm2p5_give_correct_pm2p5(){
        //Arrange
        $EXPECTED_PM2P5 = 25;

        $this->fakeReadingsPM2p5->push(['value' => 20]);
        $this->fakeReadingsPM2p5->push(['value' => 30]);

        $this->fakeReadingsPM10->push(['value' => 30]);
        $this->fakeReadingsPM10->push(['value' => 40]);

        //Act
        $this->stationController->showPublicStation(1);

        //Assert
        $this->assertEquals($EXPECTED_PM2P5, $this->station->pm2p5_raw);

    }

    public function test_raw_data_pm10_give_correct_pm10(){
        //Arrange
        $EXPECTED_PM10 = 35;

        $this->fakeReadingsPM2p5->push(['value' => 20]);
        $this->fakeReadingsPM2p5->push(['value' => 30]);

        $this->fakeReadingsPM10->push(['value' => 30]);
        $this->fakeReadingsPM10->push(['value' => 40]);

        //Act
        $this->stationController->showPublicStation(1);

        //Assert
        $this->assertEquals($EXPECTED_PM10, $this->station->pm10_raw);

    }



    public function test_showLatestAqi_method_call_stationRepository_once_with_good_parameter()
    {
        //arrange
        $this->stationRepositoryMock->shouldReceive('getReadings')->andReturn($this->fakeReadingsPM2p5);

        //expect
        $this->stationRepositoryMock->shouldReceive('findByField')->andReturn(collect($this->station));

        //act
        $this->stationController->showLatestAqi(self::STATION_ID);

        //assert
        $this->stationRepositoryMock->shouldHaveReceived('findByField')
            ->with('id', self::STATION_ID)
            ->once();
    }

    public function test_showLatestAqi_method_throw_ForbiddenAccessException_if_station_is_private()
    {
        //arrange
        $this->expectException(ForbiddenAccessException::class);

        $this->station->is_private = self::STATION_PRIVATE;

        //act
        $test = $this->stationController->showLatestAqi(self::STATION_ID);
    }

    public function test_showLatestAqi_returns_table_of_latest_aqi_of_each_pollutant()
    {
        // Arrange
        $this->stationRepositoryMock->shouldReceive('getReadings')->andReturn($this->fakeReadingsPM2p5);
        $EXPECTED_TABLE = [];

        $EXPECTED_TABLE["pm2.5"] = $this->fakeReadingsPM2p5->toArray();
        $EXPECTED_TABLE["pm10"] = $this->fakeReadingsPM10->toArray();

        foreach($EXPECTED_TABLE as $key => $pollutant){
            for($count = self::LATEST_AQI_HOURS_AGO; $count > 0; $count--){
                $EXPECTED_TABLE[$key][$count] = 0;
            }
        }

        // Act
        $actual = $this->stationController->showLatestAqi(self::STATION_ID);

        // Assert
        $this->assertEquals($EXPECTED_TABLE, $actual);
    }
}