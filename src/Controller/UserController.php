<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    #[Route('/api/user', name: 'createUser', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être administrateur pour créer un utilisateur.')]
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/user/{id}', name: 'updateUser', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être administrateur pour mettre à jour un utilisateur.')]
    public function updateUser(User $currentUser, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $updatedUser = $serializer->deserialize($request->getContent(), User::class, 'json', ['object_to_populate' => $currentUser]);
        
        $errors = $validator->validate($updatedUser);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($updatedUser->getPassword() !== $currentUser->getPassword()) {
            $updatedUser->setPassword($passwordHasher->hashPassword($updatedUser, $updatedUser->getPassword()));
        }

        $entityManager->persist($updatedUser);
        $entityManager->flush();

        return $this->json($updatedUser, JsonResponse::HTTP_OK);
    }

    #[Route('/api/user/{id}', name: 'deleteUser', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être administrateur pour supprimer un utilisateur.')]
    public function deleteUser(Request $request, SerializerInterface $serializer, User $currentUser, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($currentUser);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
