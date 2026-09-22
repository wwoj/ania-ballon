<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GalleryUploadService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\GalleryType;
use App\Entity\PictureGallery;
use App\Repository\PictureGalleryRepository;

final class GalleryController extends AbstractController
{
    #[Route('/admin/backdrop', name: 'admin_backdrop')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function backdrop(
        PictureGalleryRepository $pictureGalleryRepository,
    ): Response {
        // Get list of uploaded pictures
        $images = $pictureGalleryRepository->getSortedGalleryPictures(GalleryType::BACKDROPS);
        // dd($images);

        return $this->render('admin/gallery/index.html.twig', [
            'title' => 'Backdrops',
            'galleryType' => GalleryType::BACKDROPS,
            'images' => $images
        ]);
    }

    #[Route('/admin/animal', name: 'admin_animal')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function animals(
        PictureGalleryRepository $pictureGalleryRepository,
    ): Response {
        // Get list of uploaded pictures
        $images = $pictureGalleryRepository->findBy(['galleryType' => GalleryType::ANIMALS], ['position' => 'ASC']);

        return $this->render('admin/gallery/index.html.twig', [
            'title' => 'Animals',
            'galleryType' => GalleryType::ANIMALS,
            'images' => $images
        ]);
    }

    #[Route('/admin/decoration', name: 'admin_decoration')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function decorations(
        PictureGalleryRepository $pictureGalleryRepository,
    ): Response {
        $images = $pictureGalleryRepository->findBy(['galleryType' => GalleryType::DECORATIONS], ['position' => 'ASC']);


        return $this->render('admin/gallery/index.html.twig', [
            'title' => 'Decorations',
            'galleryType' => GalleryType::DECORATIONS,
            'images' => $images
        ]);
    }

    #[Route('/admin/upload', name: 'admin_upload')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
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

    #[Route('/admin/reorder', name: 'admin_reorder')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function reorder(
        Request $request,
        GalleryUploadService $galleryUploadService,
    ): JsonResponse {
        $positions = $request->request->get('positions');
        $positions = json_decode($positions, true);

        $galleryUploadService->reorder($positions);

        return new JsonResponse([
            'success' => true,
            'images' => 'todo',
        ]);
    }

    #[Route('/admin/delete/{id}', name: 'admin_delete')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function delete(
        PictureGallery $pictureGallery,
        GalleryUploadService $galleryUploadService,
    ): JsonResponse {

        $galleryUploadService->delete($pictureGallery);
        return new JsonResponse([
            'success' => true,
            'images' => 'todo',
        ]);
    }
}
