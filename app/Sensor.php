<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(
 *   definition="Sensor",
 *   type="object",
 *   required={"model_id", "station_id"},
 *   @SWG\Property(property="model_id", type="int"),
 *   @SWG\Property(property="station_id", type="int"),
 * )
 */
class Sensor extends Model
{
    protected $fillable = ['model_id', 'station_id'];

    public function readings(){
        return $this->hasMany('App\SensorReading');
    }

    public function model()
    {
        return $this->belongsTo('App\SensorModel');
    }

    public function getHourlyReadingsAverage($pollutantReadings, $hours){
        $hourlyAverageReadings = [];
        foreach($pollutantReadings as $key => $hourlyReadings){
            $hoursCopy = $hours;
            foreach($hourlyReadings as $pollutantAQI) {
                $averageValue = $pollutantAQI->readings->average();
                $temp = $hourlyAverageReadings;
                if($averageValue == null){
                    $averageValue = 0;
                }
                $temp[$key][$hoursCopy] = $averageValue;
                $hourlyAverageReadings = $temp;
                $hoursCopy = $hoursCopy - 1;
            }
        }
        return $hourlyAverageReadings;
    }
}
