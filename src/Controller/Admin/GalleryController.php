<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GalleryController extends AbstractController
{
    #[Route('/admin/backdrop', name: 'admin_backdrop')]
    public function backdrop(): Response
    {
        return $this->render('admin/gallery/index.html.twig', [
            'title' => 'Backdrops',
        ]);
    }

    #[Route('/admin/animal', name: 'admin_animal')]
    public function animals(): Response
    {
        return $this->render('admin/gallery/index.html.twig', [
            'title' => 'Animals',
        ]);
    }

    #[Route('/admin/decoration', name: 'admin_decoration')]
    public function decorations(): Response
    {
        return $this->render('admin/gallery/index.html.twig', [
            'title' => 'Decorations',
        ]);
    }
}
