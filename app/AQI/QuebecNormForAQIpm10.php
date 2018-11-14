<?php

namespace App\AQI;

class QuebecNormForAQIpm10 extends AQIReadings
{
    function calculateAQI($value)
    {
        return round(($value / 35) * 50);
    }
}