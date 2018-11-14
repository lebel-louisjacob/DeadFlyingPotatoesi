<?php

namespace App;

use App\Http\Resources\ReadingTypeResource;
use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(
 *   definition="SensorModel",
 *   type="object",
 *   required={"name"},
 *   @SWG\Property(property="name", type="string"),
 * )
 */
class SensorModel extends Model
{
    protected $fillable = ['name'];

    public function readingTypes()
    {
        return $this->belongsToMany('App\ReadingType', 'affiliation_reading_types');
    }
}
