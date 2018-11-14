<?php

namespace App\Http\Controllers;

use App\AQI\AQIReadings;
use App\Exceptions\ForbiddenAccessException;
use App\Http\Repositories\StationRepository;
use App\Http\Requests\StationRequest;
use App\Http\Resources\StationResource;
use App\AQI\QuebecNormForAQIpm2P5;
use App\AQI\QuebecNormForAQIpm10;
use App;

use App\Station;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class StationController extends Controller
{
    private $stationRepository;

    public function __construct(StationRepository $stationRepository)
    {
        $this->stationRepository = $stationRepository;
    }
    
    /**
     * @SWG\ Get(
     *     tags={"station"},
     *     path="/stations",
     *     summary="List all Stations",
     * @SWG\Response (
     *         response=200,
     *         description="station response",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Station")
     *         ),
     *     ),
     * )
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return StationResource::collection(Station::paginate());
    }

    public function store(StationRequest $request)
    {
        $station = $this->stationRepository->create($request->all());

        return response()->json($station, 201);
    }

    /**
     * @SWG\Get(
     *     path="/stations/{station_id}",
     *     summary="Find stations by id",
     *     tags={"station"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of station to return",
     *         in="path",
     *         name="station_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Station")
     *         ),
     *     ),
     * )
     */
    public function show(Station $station)
    {
        $station = new StationResource($station);

        return $station;
    }

    public function showAllPublicStations()
    {
        $stations = $this->stationRepository->findByField("is_private", 0);

        $stations->map(function($s){$this->calculateAqi($s); return $s;});

        return StationResource::Collection($stations);
    }

    public function showLatestAqi(int $station_id)
    {
        $stationResource = $this->getStation($station_id);

        $latestAqi = $this->calculateLatestAQI($stationResource);

        return $latestAqi;
    }

    public function showPublicStation(int $station_id)
    {
        $stationResource = $this->getStation($station_id);

        $this->calculateAqi($stationResource);

        return $stationResource;
    }

    /**
     * @SWG\Put(
     *     path="/stations/{station_id}",
     *     tags={"station"},
     *     summary="Update an existing station",
     *     description="",
     *     @SWG\Parameter(
     *         description="ID of station to update",
     *         in="path",
     *         name="station_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *     @SWG\Parameter(
     *         name="api_key",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Station not found",
     *     ),
     *     security={{"auth:api":{"scope:admin"}}}
     * )
     */
    public function update(StationRequest $request, int $station_id)
    {
        $station = $this->stationRepository->update($request->all(), $station_id);

        return response()->json($station, 200);
    }

    /**
     * @SWG\Delete(
     *     path="/stations/{station_id}",
     *     summary="Deletes a station",
     *     tags={"station"},
     *     @SWG\Parameter(
     *         description="Station id to delete",
     *         in="path",
     *         name="station_id",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *     @SWG\Parameter(
     *         name="api_key",
     *         in="header",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     security={{"auth:api":{"scope:admin"}}}
     * )
     */
    public function destroy(int $station_id)
    {
        $this->stationRepository->delete($station_id);

        return response()->json(null, 204);
    }

    private function getStation($station_id){
        $station = $this->stationRepository->findByField("id", $station_id)->first();

        if ($station == null)
        {
            throw new ModelNotFoundException();
        }

        if ($station->is_private)
        {
            throw new ForbiddenAccessException();
        }

        return new StationResource($station);
    }


    private function calculateAqi($stationResource)
    {
        $stationId = $stationResource->id;

        $hours = 4;
        $interval = 4;

        $pollutantReadings = $this->getPollutantReadings($stationId, $hours, $interval);

        $humidityReadings = $this->stationRepository->getReadings($stationId, AQIReadings::humidity, $hours, $interval);
        $temperatureReadings = $this->stationRepository->getReadings($stationId, AQIReadings::temperature, $hours, $interval);

        $stationResource->calculateAQI($pollutantReadings, $hours);
        $stationResource->calculateRating();
        $stationResource->calculateHUMIDITY($humidityReadings);
        $stationResource->calculateTEMPERATURE($temperatureReadings);
    }

    private function calculateLatestAQI($stationResource)
    {
        $stationId = $stationResource->id;

        $hours = 48;
        $interval = 1;

        $pollutantReadings = $this->getPollutantReadings($stationId, $hours, $interval);

        return $stationResource->calculateHourlyAQI($pollutantReadings, $hours);
    }

    private function getPollutantReadings($stationId, $hoursAgo, $interval){
        $pollutantReadings = [];
        foreach(AQIReadings::pollutants as $pollutant)
        {
            for($count = $hoursAgo; $count > 0; $count = $count - $interval) {
                $class = "App\AQI\QuebecNormForAQI" . str_replace(".", "p", $pollutant);
                $readings = $this->stationRepository->getReadings($stationId, $pollutant, $count, $interval);

                $pollutantAQI = new $class;
                $pollutantAQI->readings = $readings->pluck('value');

                $pollutantReadings[$pollutant][$count] = $pollutantAQI;
            }
        }
        return $pollutantReadings;
    }
}
