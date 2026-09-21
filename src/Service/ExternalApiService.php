<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class ExternalApiService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $meteoApiToken
    ) {
    }

    public function getZipCode(int $zipCode): ResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            "https://apicarto.ign.fr/api/codes-postaux/communes/{$zipCode}"
        );
    }

    public function getInsee(int $insee): ResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            "https://apicarto.ign.fr/api/cadastre/commune?code_insee={$insee}"
        );
    }

    public function getWeather(int $insee): ResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            "https://api.meteo-concept.com/api/forecast/daily?insee={$insee}",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->meteoApiToken,
                ],
            ]
        );
    }
}
