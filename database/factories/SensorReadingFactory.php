<?php

use App\SensorReading;
use Faker\Generator as Faker;

$factory->define(SensorReading::class, function (Faker $faker) {
    $sensorIDs = DB::table('sensors')->pluck('id')->toArray();
    $randomSensorID = $faker->randomElement($sensorIDs);

    $sensor = DB::table('sensors')->where('id', $randomSensorID)->first();
    $model_id = $sensor->model_id;
    $affiliations = DB::table('affiliation_reading_types')->where('sensor_model_id', $model_id);
    $readingTypeIDs = $affiliations->pluck('reading_type_id')->toArray();
    $randomReadingTypeID = $faker->randomElement($readingTypeIDs);
    $readingType = DB::table('reading_types')->where('id', $randomReadingTypeID)->first();
    $readingTypeName = $readingType->type;
    $station = DB::table('stations')->where('id', $sensor->station_id)->first();

    $stationLatitude = $station->latitude;
    $stationLongitude = $station->longitude;

    return [
        'value' => $faker->randomFloat(2, 0, 300),
        'sensor_id' => $randomSensorID,
        'type' => $readingTypeName,
        'latitude' => $stationLatitude,
        'longitude' => $stationLongitude,
        'created_at' => $faker->dateTimeBetween($startDate = '-5 days', $endDate = 'now')
    ];
});
