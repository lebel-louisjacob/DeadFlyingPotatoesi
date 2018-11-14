<?php


namespace App\Http\Repositories;


use App\History;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;

class HistoryRepository extends BaseRepository {

    function model()
    {
        return "App\\History";
    }

    function generateHistory($readings)
    {
        $history = new History();
        /*$history['lastWeekReadings'] = $this->GetLastWeekReadings($readings);
        $history['lastHourReadings'] = $this->GetLastHourReadings($readings);
        $history['lastDayReadings'] = $this->GetDailyReadings($readings);*/
        $history->lastHourReadings = $this->GetLastHourReadings($readings);
        $history->lastDayReadings = $this->GetDailyReadings($readings);
        $history->lastWeekReadings = $this->GetLastWeekReadings($readings);
        return $history;
    }


    private function GetLastHourReadings($readings)
    {
        $now = new Carbon('now');
        $lastHourReadings = [];
        foreach($readings as $reading)
        {
            $readingAttributes = $reading->attributesToArray();
            $readingDate = new Carbon($readingAttributes['created_at']);
            if($now->diffInHours($readingDate) < 1)
            {
                $value = $readingAttributes['value'];
                $lastHourReadings[$readingAttributes['created_at']] = $value;
            }
        }
        return $lastHourReadings;
    }

    private function GetDailyReadings($readings)
    {
        $now = new Carbon('now');
        $lastDayReadings = [];
        foreach($readings as $reading)
        {
            $readingAttributes = $reading->attributesToArray();
            $readingDate = new Carbon($readingAttributes['created_at']);
            if($now->diffInDays($readingDate) < 1)
            {
                $value = $readingAttributes['value'];
                $lastDayReadings[$readingAttributes['created_at']] = $value;
            }
        }
        return $lastDayReadings;
    }

    private function GetLastWeekReadings($readings)
    {
        $now = new Carbon('now');
        $lastWeekReadings = [];
        foreach($readings as $reading)
        {
            $readingAttributes = $reading->attributesToArray();
            $readingDate = new Carbon($readingAttributes['created_at']);
            $diff = $now->diffInDays($readingDate);
            if($now->diffInWeeks($readingDate) < 1)
            {
                $value = $readingAttributes['value'];
                $lastWeekReadings[$readingAttributes['created_at']] = $value;
            }
        }
        return $lastWeekReadings;
    }
}