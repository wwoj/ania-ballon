<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Implement your registration logic here

        dd('Registration logic not implemented yet.');
        return $this->render('admin/register/register.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }
}
