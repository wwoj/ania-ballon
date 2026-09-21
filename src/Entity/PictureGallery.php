<?php

namespace App\Entity;

use App\Enum\GalleryType;
use App\Repository\PictureGalleryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PictureGalleryRepository::class)]
class PictureGallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: GalleryType::class)]
    private ?GalleryType $galleryType = null;

    #[ORM\ManyToOne(inversedBy: 'pictureGalleries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Image $image = null;

    #[ORM\Column]
    private ?int $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGalleryType(): ?GalleryType
    {
        return $this->galleryType;
    }

    public function setGalleryType(GalleryType $galleryType): static
    {
        $this->galleryType = $galleryType;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
