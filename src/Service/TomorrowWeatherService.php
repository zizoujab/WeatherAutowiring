<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TomorrowWeatherService implements WeatherServiceInterface
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function getWeather(float $lat, float $lng): array
    {
        $response = $this->httpClient->request('GET' ,
            "https://api.tomorrow.io/v4/timelines?location=$lat,$lng&fields=temperatureMin,temperatureMax&timesteps=1d&units=metric&apikey=OPXCnV4CmBCybmKoM1vXmfv145aaWKqn"
        )->toArray();
        $temperatures = [];
        foreach ($response['data']['timelines'][0]['intervals'] as $interval) {
            $temperatures[(new \DateTime($interval['startTime']))->format('y-m-d')] = [
                'min' => $interval['values']['temperatureMin'],
                'max' => $interval['values']['temperatureMax'],

            ];
        }

        return $temperatures;

    }
}