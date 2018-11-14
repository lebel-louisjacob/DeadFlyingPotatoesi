<?php

use Faker\Generator as Faker;

$factory->define(App\Sensor::class, function (Faker $faker) {

    $modelIDs = DB::table('sensor_models')->pluck('id')->toArray();
    $stationIDs= DB::table('stations')->pluck('id')->toArray();

    return [
        'model_id' => $faker->randomElement($modelIDs),
        'station_id' => $faker->randomElement($stationIDs)
    ];
});
