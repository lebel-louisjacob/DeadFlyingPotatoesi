<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SensorModelResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'info' => [
                'name' => $this->name,
                'types' => $this->readingTypes->pluck('type'),
                'range' => $this->range
            ],
        ];
    }
}
