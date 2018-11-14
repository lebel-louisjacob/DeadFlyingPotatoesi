<?php

namespace App\AQI;


class QuebecNormForAQIpm2P5 extends AQIReadings
{
    public function calculateAQI($value)
    {
        return round(($value / 35) * 50);
    }
}