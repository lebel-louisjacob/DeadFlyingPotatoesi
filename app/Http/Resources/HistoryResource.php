<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class HistoryResource extends Resource
{
    public function toArray($request)
    {
        return [
            'hour' => $this->lastHourReadings,
            'day' => $this->lastDayReadings,
            'week' => $this->lastWeekReadings,
        ];
    }
}
