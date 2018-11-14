<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(
 *   definition="History",
 *   type="object",
 *   required={"lastHourReadings", "lastDayReadings", "lastWeekReadings"},
 *   @SWG\Property(property="lastHourReadings", type="float"),
 *   @SWG\Property(property="lastDayReadings", type="float"),
 *   @SWG\Property(property="lastWeekReadings", type="float"),
 * )
 */
class History extends Model
{
    protected $fillable = ['lastHourReadings', 'lastDayReadings', 'lastWeekReadings'];
}