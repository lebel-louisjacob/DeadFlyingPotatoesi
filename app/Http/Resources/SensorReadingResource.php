<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SensorReadingResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'info' => [
                'value' => $this->value,
                'sensor_id' => $this->sensor_id,
                'type' => $this->type,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'created_at' => $this->created_at
            ],
        ];
    }
}
