<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\EntityPreprocessor;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/users', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(UserRepository $userRepository): JsonResponse
    {
        return $this->json($userRepository->findAll());
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json($user);
    }

    #[IsGranted('ROLE_ROOT')]
    #[Route('', name: 'new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityPreprocessor $preprocessor,
        UserPasswordHasherInterface $passwordHasher,
        ManagerRegistry $doctrine
    ): JsonResponse {
        $user = new User();

        $this->fillUserPassword($user, $passwordHasher, $request);
        $preprocessor->populateFromRequest($user, $request);
        $preprocessor->validate($user);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user);
    }

    #[IsGranted('ROLE_ROOT')]
    #[Route('/{id}', name: 'edit', methods: ['PATCH'])]
    public function edit(
        User $user,
        Request $request,
        EntityPreprocessor $preprocessor,
        UserPasswordHasherInterface $passwordHasher,
        ManagerRegistry $doctrine
    ): JsonResponse {
        $this->fillUserPassword($user, $passwordHasher, $request);
        $preprocessor->populateFromRequest($user, $request);
        $preprocessor->validate($user);

        $doctrine->getManager()->flush();

        return $this->json($user);
    }

    #[IsGranted('ROLE_ROOT')]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(User $user, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Success',
        ]);
    }

    private function fillUserPassword(
        User $user,
        UserPasswordHasherInterface $passwordHasher,
        Request $request
    ): void {
        $data = json_decode($request->getContent(), true);

        if (!empty($data['password'])) {
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        }
    }
}
