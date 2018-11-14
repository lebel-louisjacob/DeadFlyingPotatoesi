<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(
 *   definition="City",
 *   type="object",
 *   required={"name"},
 *   @SWG\Property(property="name", type="string"),
 * )
 */
class City extends Model
{
    protected $fillable = ['name'];
}
