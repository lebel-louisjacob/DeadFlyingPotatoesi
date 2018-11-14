<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AQI\AQIRating;
/**
 * @SWG\Definition(
 *   definition="Station",
 *   type="object",
 *   required={"name", "city", "latitude", "longitude", "is_private", "updated_at"},
 *   @SWG\Property(property="name", type="string"),
 *   @SWG\Property(property="city", type="string"),
 *   @SWG\Property(property="latitude", type="float"),
 *   @SWG\Property(property="longitude", type="float"),
 *   @SWG\Property(property="is_private", type="boolean"),
 *   @SWG\Property(property="updated_at", type="datetime"),
 * )
 */
class Station extends Model
{
    protected $fillable = ['name', 'city', 'latitude', 'longitude', 'is_private', 'updated_at'];

    public function sensors(){
        return $this->hasMany('App\Sensor');
    }

    public function comments(){
        return $this->hasMany('App\Comment');
    }


    public function calculateAQI($pollutantReadings, $hours){
        $this->aqiMax = 0;
        $this->aqiAverage = 0;
        $this->pm2p5 = 0;
        $this->pm10 = 0;
        $this->pm2p5_raw = 0;
        $this->pm10_raw = 0;

        foreach($pollutantReadings as $key => $pollutantAQI){
            $pollutantAQI = $pollutantAQI[$hours];
            $maxValue = $pollutantAQI->readings->max();
            $maxAQI = $pollutantAQI->calculateAQI($maxValue);

            if($maxAQI > $this->aqiMax){
                $this->aqiMax = $maxAQI;
            }

            $averageValue = $pollutantAQI->readings->average();
            $averageAQI = $pollutantAQI->calculateAQI($averageValue);
            if($averageValue !== null){
                if($key == 'pm2.5'){
                    $this->pm2p5 = $averageAQI;
                    $this->pm2p5_raw = round($averageValue,2);
                }
                else{
                    $this->pm10 = $averageAQI;
                    $this->pm10_raw = round($averageValue,2);
                }
            }

            if($averageAQI > $this->aqiAverage){
                $this->aqiAverage = $averageAQI;
            }
        }
    }

    public function calculateHUMIDITY($pollutantReadings){
        $this->aqiHumidity = 0;
        $count = 0;

        foreach($pollutantReadings as $humidity){
            $this->aqiHumidity += $humidity['value'];
            $count ++;
        }
        if($count > 0){
            $this->aqiHumidity/=$count;
            $this->aqiHumidity = round($this->aqiHumidity,2);
        }
    }

    public function calculateTEMPERATURE($pollutantReadings){
        $this->aqiTemperature = 0;
        $count = 0;

        foreach($pollutantReadings as $humidity){
            $this->aqiTemperature += $humidity['value'];
            $count ++;
        }
        if($count > 0){
            $this->aqiTemperature/=$count;
            $this->aqiTemperature = round($this->aqiTemperature,2);
        }
    }

    public function calculateHourlyAQI($pollutantReadings, $hours){
        $latestReadings = [];
        foreach($pollutantReadings as $key => $hourlyReadings){
            $hoursCopy = $hours;
            foreach($hourlyReadings as $pollutantAQI) {
                $averageValue = $pollutantAQI->readings->average();
                $averageAQI = $pollutantAQI->calculateAQI($averageValue);
                $temp = $latestReadings;
                $temp[$key][$hoursCopy] = $averageAQI;
                $latestReadings = $temp;
                $hoursCopy = $hoursCopy - 1;
            }
        }
        return $latestReadings;
    }

    public function calculateRating(){
        $rating = AQIRating::getRating($this->aqiAverage);
        $this->aqiColor = $rating['color'];
        $this->aqiLabel = $rating['label'];
    }
}
