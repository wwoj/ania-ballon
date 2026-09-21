<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    private ?string $originalName = null;

    #[ORM\Column(length: 255)]
    private ?string $mimetype = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, PictureGallery>
     */
    #[ORM\OneToMany(targetEntity: PictureGallery::class, mappedBy: 'image')]
    private Collection $pictureGalleries;

    public function __construct()
    {
        $this->pictureGalleries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    public function setMimetype(string $mimetype): static
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, PictureGallery>
     */
    public function getPictureGalleries(): Collection
    {
        return $this->pictureGalleries;
    }

    public function addPictureGallery(PictureGallery $pictureGallery): static
    {
        if (!$this->pictureGalleries->contains($pictureGallery)) {
            $this->pictureGalleries->add($pictureGallery);
            $pictureGallery->setImage($this);
        }

        return $this;
    }

    public function removePictureGallery(PictureGallery $pictureGallery): static
    {
        if ($this->pictureGalleries->removeElement($pictureGallery)) {
            // set the owning side to null (unless already changed)
            if ($pictureGallery->getImage() === $this) {
                $pictureGallery->setImage(null);
            }
        }

        return $this;
    }
}
