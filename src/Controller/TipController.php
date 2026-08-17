<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Tip;
use App\Entity\Month;
use App\Repository\TipRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    #[ParamConverter('month', options: ['mapping' => ['monthNumber' => 'number']])]
    public function getTipsListByMonth(Month $month, TipRepository $tipRepository, SerializerInterface $serializer): JsonResponse
    {
        $tipsList = $tipRepository->findByMonth($month->getNumber());
        $jsonTipsList = $serializer->serialize($tipsList, 'json', ['groups' => 'getTips']);
        
        if (empty($tipsList)) {
            return new JsonResponse(['message' => 'Aucun conseil trouvé pour ce mois.'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse($jsonTipsList, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/tips', name: 'createTip', methods: ['POST'])]
    public function createTip(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // Extract month before deserialze the JSON data into a Tip entity
        $monthsData = $data['months'] ?? [];
        unset($data['months']); // Remove months from data to avoid deserialization issues

        $tip = $serializer->deserialize(json_encode($data), Tip::class, 'json');
        
        foreach ($monthsData as $monthNumber) {
            $month = $entityManager->getRepository(Month::class)->findOneBy(['number' => $monthNumber]);
            if ($month) {
                $tip->addMonth($month);
            }
        }

        $errors = $validator->validate($tip);
        if (count($errors) > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($tip);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Le conseil a été créé avec succès.'], JsonResponse::HTTP_CREATED);
    }
}
