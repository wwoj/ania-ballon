<?php

namespace App\Repository;

use App\Entity\PictureGallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Enum\GalleryType;

/**
 * @extends ServiceEntityRepository<PictureGallery>
 */
class PictureGalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PictureGallery::class);
    }

    public function getNextPosition(GalleryType $galleryType): int
    {
        $lastPosition = $this->createQueryBuilder('pg')
            ->select('MAX(pg.position)')
            ->where('pg.galleryType = :galleryType')
            ->setParameter('galleryType', $galleryType)
            ->getQuery()
            ->getSingleScalarResult();

        return ((int) $lastPosition) + 1;
    }

    public function getSortedGalleryPictures(GalleryType $galleryType): array
    {
        return $this->createQueryBuilder('pg')
            ->join('pg.image', 'i')
            ->where('pg.galleryType = :galleryType')
            ->setParameter('galleryType', $galleryType)
            ->addOrderBy('pg.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
