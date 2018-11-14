<?php

namespace App\AQI;

abstract class AQIReadings
{
    public $readings;
    const pollutants = ["pm2.5", "pm10"];
    const humidity = 'Humidity';
    const temperature = 'Temperature';

    abstract public function calculateAQI($value);
}