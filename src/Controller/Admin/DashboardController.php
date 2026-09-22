<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
