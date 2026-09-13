<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

final class RegisterController extends AbstractController
{
    #[Route('/admin/register', name: 'app_admin_register')]
    public function index(): Response
    {
        return $this->render('admin/register/index.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }

    #[Route('/admin/registers', name: 'admin_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
    ): JsonResponse {
        $user = new User();

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $request->request->getString('password')
        );

        $user->setPassword($hashedPassword);
        $user->setEmail($request->request->getString('email'));

        $userRepository->save($user, true);

        return $this->json([
            'success' => true,
            'hash_created' => $hashedPassword !== '',
        ]);
    }
}
