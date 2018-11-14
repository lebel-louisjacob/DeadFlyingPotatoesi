<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-14
 * Time: 9:46 AM
 */

namespace App\Http\Repositories;

use Illuminate\Support\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;

class StationRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "App\\Station";
    }

    function getSensors($stationId)
    {
        $station = $this->find($stationId);

        return $station->sensors;
    }

    function getReadings($stationId, $type, $hoursAgo, $interval)
    {
        $MIN_EPOCH = Carbon::now()->subHours($hoursAgo);
        $MAX_EPOCH = Carbon::now()->subHours($hoursAgo - $interval);

        $readings = collect([]);

        $sensors = $this->getSensors($stationId);
        foreach($sensors as $sensor)
        {
            $sensorReadings = $sensor->readings
                ->where("type",$type)
                ->where("created_at",">",$MIN_EPOCH)
                ->where("created_at","<",$MAX_EPOCH);

            $readings = $readings->concat($sensorReadings);
        }

        return $readings;
    }
}