<?php

use App\Station;
use Faker\Generator as Faker;

$factory->define(Station::class, function (Faker $faker) {

    $CitiesName= DB::table('cities')->pluck('name')->toArray();

    return [
        'name' => $faker->name,
        'city' => $faker->randomElement($CitiesName),
        'latitude' => $faker->randomFloat(7, 46.7, 46.85),
        'longitude' => $faker->randomFloat(7, -71.3, -71.2),
        'is_private' => 0
    ];
});
