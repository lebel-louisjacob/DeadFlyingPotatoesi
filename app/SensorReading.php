<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(
 *   definition="SensorReading",
 *   type="object",
 *   required={"value", "sensor_id", "latitude", "longitude", "type"},
 *   @SWG\Property(property="value", type="float"),
 *   @SWG\Property(property="sensor_id", type="unsignedint"),
 *   @SWG\Property(property="latitude", type="float"),
 *   @SWG\Property(property="longitude", type="float"),
 *   @SWG\Property(property="type", type="string"),
 * )
 */
class SensorReading extends Model
{
    protected $fillable = ['value', 'sensor_id', 'latitude', 'longitude', 'type'];

    public function type(){
        return $this->hasOne('App\ReadingType');
    }
}
