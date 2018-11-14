<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SensorResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'info' => [
                'name' => $this->model->name,
                'station_id' => $this->station_id,
                'types' => $this->model->readingTypes->pluck('type'),
                'created_at' => $this->created_at
            ]
        ];
    }
}
