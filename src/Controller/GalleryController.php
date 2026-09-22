<?php

namespace App\Controller;

use App\Enum\GalleryType;
use App\Repository\PictureGalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GalleryController extends AbstractController
{
    #[Route('/gallery', name: 'gallery', methods: ['GET'])]
    #[Route('/pl/gallery', name: 'gallery_pl', methods: ['GET'])]
    public function index(
        PictureGalleryRepository $pictureGalleryRepository,
    ): Response {

        // Get list of pictures :)
        $pictureList = $pictureGalleryRepository->getSortedGalleryPictures(GalleryType::BACKDROPS);

        return $this->render('gallery/index.html.twig', [
            'controller_name' => 'GalleryController',
            'title' => 'Gallery',
            'pictures' => $pictureList,
        ]);
    }

    #[Route(
        path: [
            'en' => '/gallery/backdrop',
            'pl' => '/pl/gallery/backdrop',
        ],
        name: 'app_backdrop',
        methods: ['GET'],
    )]
    public function backdrop(
        PictureGalleryRepository $pictureGalleryRepository,
    ): Response {
        $pictures = $pictureGalleryRepository->getSortedGalleryPictures(GalleryType::BACKDROPS);

        return $this->render('gallery/index.html.twig', [
            'controller_name' => 'GalleryController',
            'title' => 'backdrop',
            'pictures' => $pictures,
        ]);
    }

    #[Route(
        path: [
            'en' => '/gallery/animal',
            'pl' => '/pl/gallery/animal',
        ],
        name: 'app_animal',
        methods: ['GET'],
    )]
    public function animal(
        PictureGalleryRepository $pictureGalleryRepository,
    ): Response {
        $pictures = $pictureGalleryRepository->getSortedGalleryPictures(GalleryType::ANIMALS);

        return $this->render('gallery/index.html.twig', [
            'controller_name' => 'GalleryController',
            'title' => 'animal',
            'pictures' => $pictures,

        ]);
    }

    #[Route(
        path: [
            'en' => '/gallery/event',
            'pl' => '/pl/gallery/event',
        ],
        name: 'app_event',
        methods: ['GET'],
    )]
    public function event(
        PictureGalleryRepository $pictureGalleryRepository,
    ): Response {
        $pictures = $pictureGalleryRepository->getSortedGalleryPictures(GalleryType::DECORATIONS);

        return $this->render('gallery/index.html.twig', [
            'controller_name' => 'GalleryController',
            'title' => 'event',
            'pictures' => $pictures,

        ]);
    }
}
