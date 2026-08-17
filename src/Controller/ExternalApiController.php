<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ExternalApiController extends AbstractController
{
    #[Route('/api/external/zipCode/{zipCode}', name: 'external_api_zip_code', methods: ['GET'])]
    public function index(int $zipCode, HttpClientInterface $httpClient): JsonResponse
    {
        $response = $httpClient->request(
            'GET', "https://apicarto.ign.fr/api/codes-postaux/communes/{$zipCode}"
        );
        return new JsonResponse($response->getContent(), $response->getStatusCode(), [], true);
    }
}
