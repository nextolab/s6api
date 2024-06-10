<?php

namespace App\Controller\Api\V1;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1', name: 'security_')]
class SecurityController extends AbstractController
{
    #[Route('', name: 'ping', methods: ['GET'])]
    public function ping(): JsonResponse
    {
        return $this->json([
            'code' => 200,
            'message' => 'Success',
        ]);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return $this->json($this->getUser());
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): void
    {
        // it will never be executed!
    }

    #[Route('/profile', name: 'profile', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        return $this->json($this->getUser());
    }

    #[Route('/password', name: 'password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): JsonResponse {
        $user = $this->getUser();
        $newPassword = $passwordHasher->hashPassword($user, $request->getContent());

        $userRepository->upgradePassword($user, $newPassword);

        return $this->json($request->getContent());
    }
}
