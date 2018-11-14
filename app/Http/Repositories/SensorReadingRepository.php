<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-14
 * Time: 11:48 AM
 */

namespace App\Http\Repositories;


use Prettus\Repository\Eloquent\BaseRepository;

class SensorReadingRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "App\\SensorReading";
    }
}