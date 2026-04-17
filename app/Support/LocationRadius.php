<?php

namespace App\Support;

class LocationRadius
{
    public static function haversineKm(float $latitudeOne, float $longitudeOne, float $latitudeTwo, float $longitudeTwo): float
    {
        $earthRadiusKm = 6371;

        $deltaLatitude = deg2rad($latitudeTwo - $latitudeOne);
        $deltaLongitude = deg2rad($longitudeTwo - $longitudeOne);

        $a = sin($deltaLatitude / 2) ** 2
            + cos(deg2rad($latitudeOne)) * cos(deg2rad($latitudeTwo))
            * sin($deltaLongitude / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
