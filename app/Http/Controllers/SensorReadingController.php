<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenAccessException;
use App\Http\Repositories\SensorReadingRepository;
use App\Http\Repositories\SensorRepository;
use App\Http\Repositories\StationRepository;
use App\Http\Requests\SensorReadingRequest;
use App\Http\Resources\SensorReadingResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Prettus\Validator\Exceptions\ValidatorException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SensorReadingController extends Controller
{
    private $stationRepository;
    private $sensorRepository;
    private $sensorReadingRepository;

    public function __construct(StationRepository $stationRepository, SensorRepository $sensorRepository,
                                SensorReadingRepository $sensorReadingRepository)
    {
        $this->stationRepository = $stationRepository;
        $this->sensorRepository = $sensorRepository;
        $this->sensorReadingRepository = $sensorReadingRepository;
    }

    public function showAllPublicSensorReadings(int $station_id, int $sensor_id)
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

        $readings = $this->sensorReadingRepository->findByField("sensor_id", $sensor_id);
        $this->ValidateExistence($readings);

        return SensorReadingResource::Collection(collect($readings));
    }

    public function showPublicReadingsByType(int $station_id, int $sensor_id, string $type)
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

        return SensorReadingResource::Collection(collect($readings));
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

    public function store(int $station_id, int $sensor_id, SensorReadingRequest $request)
    {
        $station = $this->stationRepository->findWhere([
            'id' => $station_id,
        ]);
        $this->ValidateExistence($station);

        $sensors = $this->sensorRepository->findWhere([
            'station_id' => $station_id,
            'id' => $sensor_id,
        ]);
        $this->ValidateExistence($sensors);

        $readingTypes = $this->sensorRepository->getReadingTypes($sensors->first());
        $this->ValidateRequest($readingTypes, $request);

        $completeRequest = array_merge($request->all(), ["sensor_id" => $sensor_id]);
        $response = $this->sensorReadingRepository->create($completeRequest);

        return new \Illuminate\Http\JsonResponse($response, 201);
    }

    private function ValidateRequest($readingTypes, SensorReadingRequest $request): void
    {
        $typeToSearch = $request["type"];
        if (!($this->find($typeToSearch, $readingTypes)))
        {
            throw new BadRequestHttpException();
        }
    }

    private function find($typeToSearch, $readingTypes)
    {
        foreach ($readingTypes as $readingType)
        {
            if ($readingType == $typeToSearch)
            {
                return true;
            }
        }
        return false;
    }


}
