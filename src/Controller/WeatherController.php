<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ExternalApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Validator\Constraints\ValidInsee;

final class WeatherController extends AbstractController
{
    #[Route('/api/weather', name: 'app_weather')]
    public function weatherByUser(ExternalApiService $externalApiService, TagAwareCacheInterface $cache): JsonResponse
    {
        $user = $this->getUser();
        $insee = $user instanceof User ? $user->getInsee() : null;

        $idCache = "weather_{$insee}";
        $cityWeatherJson = $cache->get($idCache, function (ItemInterface $item) use ($externalApiService, $insee) {
            $item->tag("weather_{$insee}");
            $item->expiresAfter(900); // Cache for 15 minutes
            return $externalApiService->getWeather((int)$insee)->toArray();
        });

        return $this->json([
            'message' => "Météo pour le code INSEE {$insee}",
            'weather' => $cityWeatherJson,
        ]);
    }

    #[Route('/api/weather/{insee}', name: 'app_weather_by_insee')]
    public function weatherByInsee(string $insee, ExternalApiService $externalApiService, TagAwareCacheInterface $cache, ValidatorInterface $validator): JsonResponse
    {
        // Insee check
        $inseeConstraint = new ValidInsee();
        $errors = $validator->validate($insee, $inseeConstraint);
        if (count($errors) > 0) {
            return $this->json([
                'message' => "Code INSEE invalide {$insee}",
            ], 400);
        }

        $insee = (int) $insee;

        $idCache = "weather_{$insee}";
        $cityWeatherJson = $cache->get($idCache, function (ItemInterface $item) use ($externalApiService, $insee) {
            $item->tag("weather_{$insee}");
            $item->expiresAfter(900); // Cache for 15 minutes
            return $externalApiService->getWeather($insee)->toArray();
        });
        return $this->json([
            'message' => "Météo pour le code INSEE {$insee}",
            'weather' => $cityWeatherJson,
        ]);
    }
}
