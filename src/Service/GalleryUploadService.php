<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\PictureGallery;
use App\Enum\GalleryType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PictureGalleryRepository;

use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly final class GalleryUploadService
{
    public function __construct(
        private FileUploader $fileUploader,
        private EntityManagerInterface $entityManager,
        private PictureGalleryRepository $pictureGalleryRepository,
    ) {}

    public function upload(array $files, GalleryType $galleryType): array
    {
        $position = $this->pictureGalleryRepository->getNextPosition($galleryType);

        $uploadedImages = [];

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            // Get file information BEFORE moving the file
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $size = $file->getSize();

            // Move file to upload directory
            $filename = $this->fileUploader->upload($file);

            // Create Image entity
            $image = new Image();

            $image
                ->setFilename($filename)
                ->setOriginalName($originalName)
                ->setMimetype($mimeType)
                ->setSize($size)
                ->setCreatedAt(new \DateTimeImmutable());

            // Create PictureGallery entry
            $pictureGallery = new PictureGallery();

            $pictureGallery
                ->setGalleryType($galleryType)
                ->setImage($image)
                ->setPosition($position);

            $this->entityManager->persist($image);
            $this->entityManager->persist($pictureGallery);

            $uploadedImages[] = [
                'filename' => $filename,
                'originalName' => $originalName,
                'position' => $position,
            ];

            $position++;
        }

        $this->entityManager->flush();

        return $uploadedImages;
    }
}
