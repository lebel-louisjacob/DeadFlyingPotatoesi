<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenAccessException;
use App\Http\Repositories\SensorRepository;
use App\Http\Repositories\StationRepository;
use App\Http\Resources\SensorResource;
use App\Sensor;
use App\Station;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SensorController extends Controller
{
    private $stationRepository;
    private $sensorRepository;

    public function __construct(StationRepository $stationRepository, SensorRepository $sensorRepository)
    {
        $this->stationRepository = $stationRepository;
        $this->sensorRepository = $sensorRepository;
    }

    public function showAllPublicSensors(string $station_id)
    {
        $this->verifyStation($station_id);

        $sensors = $this->sensorRepository->findByField('station_id', $station_id);

        return SensorResource::Collection(collect($sensors));
    }

    public function showPublicSensor(string $station_id, string $sensor_id)
    {
        $this->verifyStation($station_id);
        $this->verifySensor($station_id, $sensor_id);
        $sensor = collect($this->sensorRepository->findWhere([
            'station_id' => $station_id,
            'id' => $sensor_id
        ]));

        return new SensorResource($sensor->first());
    }

    public function showLatestValues(string $station_id, string $sensor_id)
    {
        $this->verifyStation($station_id);
        $this->verifySensor($station_id, $sensor_id);
        $sensorResource = $this->showPublicSensor($station_id, $sensor_id);

        $hoursAgo = 48;
        $interval = 1;

        $latestReadings = $this->getPollutantReadings($sensorResource, $hoursAgo, $interval);

        return $sensorResource->getHourlyReadingsAverage($latestReadings, $hoursAgo);
    }

    private function getPollutantReadings($sensor, $hoursAgo, $interval){
        $pollutantReadings = [];
        $types = $this->sensorRepository->getReadingTypes($sensor);
        foreach($types as $pollutant)
        {
            for($count = $hoursAgo; $count > 0; $count = $count - $interval) {
                $class = "App\AQI\QuebecNormForAQI" . str_replace(".", "p", $pollutant);
                $readings = $this->sensorRepository->getReadings($sensor, $pollutant, $count, $interval);

                $pollutantAQI = new $class;
                $pollutantAQI->readings = $readings->pluck('value');

                $pollutantReadings[$pollutant][$count] = $pollutantAQI;
            }
        }
        return $pollutantReadings;
    }

    private function verifyStation(string $station_id){
        $station = $this->stationRepository->findWhere([
            'id' => $station_id
        ]);

        if(count($station) == 0)
        {
            throw new ModelNotFoundException();
        }

        $sensors = $this->sensorRepository->findWhere(['station_id' => $station_id]);
        if(count($sensors) == 0)
        {
            throw new ModelNotFoundException();
        }

        $station = $this->stationRepository->findWhere([
            'id' => $station_id,
            'is_private' => 0
        ]);

        if(count($station) == 0)
        {
            throw new ForbiddenAccessException();
        }
    }

    private function verifySensor(string $station_id, string $sensor_id){
        $station = $this->sensorRepository->findWhere([
            'id' => $sensor_id
        ]);

        if(count($station) == 0)
        {
            throw new ModelNotFoundException();
        }

        $sensors = $this->sensorRepository->findWhere([
            'station_id' => $station_id,
            'id' => $sensor_id
        ]);
        if(count($sensors) == 0)
        {
            throw new ModelNotFoundException();
        }
    }

}
