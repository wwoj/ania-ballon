<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AboutUsController extends AbstractController
{
    #[Route('/about-us', name: 'about_us', methods: ['GET'])]
    #[Route('/pl/about-us', name: 'about_us_pl', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('about_us/index.html.twig', [
            'controller_name' => 'AboutUsController',
        ]);
    }
}
