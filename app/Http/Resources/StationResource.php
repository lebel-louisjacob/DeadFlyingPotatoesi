<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class StationResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'is_private' => $this->is_private,
            'aqi' => [
                'average' => $this->aqiAverage,
                'color' => $this->aqiColor,
                'label' => $this->aqiLabel,
                'maximum' => $this->aqiMax,
                'humidity' => $this->aqiHumidity,
                'temperature' => $this->aqiTemperature,
                'pm2p5' => $this->pm2p5,
                'pm10' => $this->pm10
            ],
            'info' => [
                'city' => $this->city,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'name' => $this->name,
                'updated_at' => $this->updated_at
                ],
            'raw_data' => [
                'pm2p5' => $this->pm2p5_raw,
                'pm10' => $this->pm10_raw
            ]
        ];

    }
}
