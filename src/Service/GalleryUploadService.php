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
                'pictureGallery' => $pictureGallery,
                'filename' => $filename,
                'originalName' => $originalName,
                'position' => $position,
            ];

            $position++;
        }

        $this->entityManager->flush();

        foreach ($uploadedImages as $index => $uploadedImage) {
            $pictureGallery = $uploadedImage['pictureGallery'];
            $uploadedImages[$index] = [
                'pictureGalleryId' => $pictureGallery->getId(),
                'filename' => $uploadedImage['filename'],
                'originalName' => $uploadedImage['originalName'],
                'position' => $uploadedImage['position'],
            ];
        }

        return $uploadedImages;
    }

    public function reorder(array $positions): void
    {
        foreach ($positions as $item) {
            $gallery = $this->pictureGalleryRepository->find($item['id']);

            if (!$gallery) {
                continue;
            }

            $gallery->setPosition($item['position']);
        }

        $this->entityManager->flush();
    }

    public function delete(PictureGallery $pictureGallery): void
    {
        $image = $pictureGallery->getImage();
        $filename = $image?->getFilename();
        $galleryType = $pictureGallery->getGalleryType();

        if ($image) {
            $image->removePictureGallery($pictureGallery);
        }

        $this->entityManager->remove($pictureGallery);

        if ($image && $image->getPictureGalleries()->isEmpty()) {
            $this->entityManager->remove($image);
        }

        $this->entityManager->flush();

        if ($galleryType) {
            $this->normalizePositions($galleryType);
        }

        if ($filename && $image && $image->getPictureGalleries()->isEmpty()) {
            $this->fileUploader->delete($filename);
        }
    }

    public function normalizePositions(GalleryType $galleryType): void
    {
        $galleries = $this->pictureGalleryRepository->findBy(
            ['galleryType' => $galleryType],
            ['position' => 'ASC']
        );

        foreach ($galleries as $index => $gallery) {
            $gallery->setPosition($index + 1);
        }

        $this->entityManager->flush();
    }
}
