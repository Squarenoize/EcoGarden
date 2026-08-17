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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être administrateur pour créer un conseil.')]
    public function createTip(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $requestData = $this->extractMonthsFromRequest(json_decode($request->getContent(), true));

        $tip = $serializer->deserialize(json_encode($requestData['data']), Tip::class, 'json');

        foreach ($requestData['months'] as $monthNumber) {
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

    #[Route('/api/tips/{id}', name: 'deleteTip', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être administrateur pour supprimer un conseil.')]
    public function deleteTip(Tip $tip, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($tip);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/tips/{id}', name: 'updateTip', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être administrateur pour mettre à jour un conseil.')]
    public function updateTip(Request $request, Tip $tip, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $requestData = $this->extractMonthsFromRequest(
        json_decode($request->getContent(), true)
        );

        $tip = $serializer->deserialize(json_encode($requestData['data']), Tip::class, 'json', ['object_to_populate' => $tip]);

        foreach ($requestData['months'] as $monthNumber) {
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

        return new JsonResponse(['message' => 'Le conseil a été mis à jour avec succès.'], JsonResponse::HTTP_OK);
    }

    private function extractMonthsFromRequest(array $data): array
    {
        $monthsData = $data['months'] ?? [];
        unset($data['months']);
        
        return [
            'data' => $data,
            'months' => $monthsData
        ];
    }
}
