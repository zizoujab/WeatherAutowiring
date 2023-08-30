<?php

namespace App\Service;

interface WeatherServiceInterface
{
    public function getWeather(float $lat, float $lng) : array;

}