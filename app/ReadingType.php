<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(
 *   definition="ReadingType",
 *   type="object",
 *   required={"type"},
 *   @SWG\Property(property="type", type="string"),
 *
 * )
 */
class ReadingType extends Model
{
    protected $fillable = ['type'];
}