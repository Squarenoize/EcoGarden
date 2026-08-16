<?php

namespace App\Controller;

use App\Repository\TipRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TipController extends AbstractController
{
    #[Route('/api/tips', name: 'tipsList', methods: ['GET'])]
    public function getTipsList(TipRepository $tipRepository, SerializerInterface $serializer): JsonResponse
    {
        $tipsList = $tipRepository->findAll();
        $jsonTipsList = $serializer->serialize($tipsList, 'json', ['groups' => 'getTips']);

        return new JsonResponse($jsonTipsList, JsonResponse::HTTP_OK, [], true);
    }
}
