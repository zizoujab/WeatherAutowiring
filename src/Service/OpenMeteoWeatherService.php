<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenMeteoWeatherService implements WeatherServiceInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly int                 $days,
        private readonly string              $temperatureUnit
    )
    {
    }

    /**
     * the returned array  will contain the day as key and  an array
     * containing min/max temperature as value
     * Example :
     * [
     *  '2023-08-26' => ['min' => 12.4, 'max' => 20.1]
     * ]
     */
    public function getWeather(float $lat, float $lng) : array
    {

        $query =  [
            'latitude' => $lat,
            'longitude' => $lng,
            'daily' => 'temperature_2m_max,temperature_2m_min',
            'timezone' => 'Europe/Paris',
            'forecast_days' => $this->days,
            'temperature_unit' => $this->temperatureUnit,
        ];
        $response = $this->httpClient->request('GET',
            'https://api.open-meteo.com/v1/forecast',
            ['query' => $query]
        )->toArray();

        $weatherArray = [];
        $daily = $response['daily'];
        foreach ($daily['time'] as $key => $date) {
            $weatherArray[$date]= [
                'min' => $daily['temperature_2m_min'][$key],
                'max' => $daily['temperature_2m_max'][$key],
            ];

        }

        return $weatherArray;
    }
}