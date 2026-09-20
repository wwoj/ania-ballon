<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;


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
    public function upload(Request $request, FileUploader $fileUploader): JsonResponse
    {
        // Get list of uploaded files
        $files = $request->files->all('images');

        $uploadedFiles = [];

        // Loop over fiels and save on server
        foreach ($files as $file) {
            // need to create unique name standard plus timestamp
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $filename = $fileUploader->upload($file);

            $uploadedFiles[] = $filename;
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'test',
            'files' => $uploadedFiles,
        ]);
    }
}
