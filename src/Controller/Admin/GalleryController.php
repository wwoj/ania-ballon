<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
use App\Service\GalleryUploadService;
use Doctrine\ORM\EntityManagerInterface;

use App\Enum\GalleryType;
use App\Repository\PictureGalleryRepository;

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

    #[Route('/admin/upload', name: 'admin_upload')]
    public function upload(
        Request $request,
        GalleryUploadService $galleryUploadService,
    ): JsonResponse {
        $files = $request->files->all('images');

        if (empty($files)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No files uploaded.',
            ], 400);
        }

        $galleryTypeValue = $request->request->get('galleryType');

        if (!$galleryTypeValue) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Gallery type is required.',
            ], 400);
        }

        $uploadedImages = [];

        try {
            $galleryType = GalleryType::from($galleryTypeValue);
        } catch (\ValueError) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid gallery type.',
            ], 400);
        }

        $uploadedImages = $galleryUploadService->upload($files, $galleryType);

        return new JsonResponse([
            'success' => true,
            'images' => $uploadedImages,
        ]);
    }
}
