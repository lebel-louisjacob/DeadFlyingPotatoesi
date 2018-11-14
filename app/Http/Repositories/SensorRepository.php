<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-14
 * Time: 11:22 AM
 */

namespace App\Http\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Carbon;

class SensorRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "App\\Sensor";
    }

    function getReadingTypes($sensor)
    {
        $model = $sensor->model;
        return $model->readingTypes()->pluck('type');
    }

    function getReadings($sensor, $type, $hoursAgo, $interval)
    {
        $MIN_EPOCH = Carbon::now()->subHours($hoursAgo);
        $MAX_EPOCH = Carbon::now()->subHours($hoursAgo - $interval);

        $readings = collect([]);

        $sensorReadings = $sensor->readings
            ->where("type",$type)
            ->where("created_at",">",$MIN_EPOCH)
            ->where("created_at","<",$MAX_EPOCH);

            $readings = $readings->concat($sensorReadings);

        return $readings;
    }

    function getTypes($sensor){
        return $sensor->model->readingTypes->pluck('type');
    }
}