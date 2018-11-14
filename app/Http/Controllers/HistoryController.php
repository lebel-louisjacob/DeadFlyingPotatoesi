<?php

namespace App\Http\Controllers;


use App\Exceptions\ForbiddenAccessException;
use App\History;
use App\Http\Repositories\HistoryRepository;
use App\Http\Repositories\SensorReadingRepository;
use App\Http\Repositories\SensorRepository;
use App\Http\Repositories\StationRepository;
use App\Http\Resources\HistoryResource;
use App\SensorReading;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HistoryController extends Controller
{
    private $stationRepository;
    private $sensorRepository;
    private $sensorReadingRepository;
    private $historyRepository;

    public function __construct(StationRepository $stationRepository, SensorRepository $sensorRepository,
                                SensorReadingRepository $sensorReadingRepository, HistoryRepository $historyRepository)
    {
        $this->stationRepository = $stationRepository;
        $this->sensorRepository = $sensorRepository;
        $this->sensorReadingRepository = $sensorReadingRepository;
        $this->historyRepository = $historyRepository;
    }
    public function showHistory(int $station_id, int $sensor_id, string $type)
    {
        $sensor = $this->sensorRepository->findWhere([
            'station_id' => $station_id,
            'id' => $sensor_id
        ]);
        $this->ValidateExistence($sensor);

        $station = $this->stationRepository->findWhere([
            'id' => $station_id,
            'is_private' => 0
        ]);
        $this->ValidateAccess($station);

        $readings = $this->sensorReadingRepository->findWhere([
            'sensor_id' => $sensor_id,
            'type' => $type
        ]);
        $this->ValidateExistence($readings);

        $history = $this->historyRepository->generateHistory($readings);
        return HistoryResource::make($history);
    }

    private function ValidateExistence($collection): void
    {
        if (count($collection) == 0) {
            throw new ModelNotFoundException();
        }
    }

    private function ValidateAccess($station): void
    {
        if (count($station) == 0) {
            throw new ForbiddenAccessException();
        }
    }
}