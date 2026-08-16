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
    public function getCurrentTipsList(TipRepository $tipRepository, SerializerInterface $serializer): JsonResponse
    {
        $currentMonth = (int) date('n');

        $tipsList = $tipRepository->findByMonth($currentMonth);
        $jsonTipsList = $serializer->serialize($tipsList, 'json', ['groups' => 'getTips']);

        return new JsonResponse($jsonTipsList, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/tips/{monthNumber}', name: 'tipsListByMonth', methods: ['GET'])]
    public function getTipsListByMonth(int $monthNumber, TipRepository $tipRepository, SerializerInterface $serializer): JsonResponse
    {
        $tipsList = $tipRepository->findByMonth($monthNumber);
        $jsonTipsList = $serializer->serialize($tipsList, 'json', ['groups' => 'getTips']);
        
        if (empty($tipsList)) {
            return new JsonResponse(['message' => 'Aucun conseil trouvé pour ce mois.'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse($jsonTipsList, JsonResponse::HTTP_OK, [], true);
    }
}
