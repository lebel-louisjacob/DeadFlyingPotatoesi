<?php

namespace App\AQI;

class AQIRating {

    public static function getRating($aqi){
        $ratings = collect([
            ["label"=>"Bon","color"=>"#006966", "max" => 50],
            ["label"=>"Modéré","color"=>"#FFDE33", "max" => 100],
            ["label"=>"Mauvais pour les groupes sensibles","color"=>"#FF9933", "max" => 150],
            ["label"=>"Mauvais","color"=>"#CC0033", "max" => 200],
            ["label"=>"Très mauvais","color"=>"#660099", "max" => 300],
            ["label"=>"Dangereux","color"=>"#7E0033", "max" => 1000]
        ]);

        $rating = $ratings->where("max",">", $aqi)->first();
        $level = $rating['label'];
        $color = $rating['color'];

        return ["label" => $level, "color" => $color];
    }

}