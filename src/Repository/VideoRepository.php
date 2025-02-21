<?php

namespace App\Repository;

use App\Entity\Video; // Changé de Videos à Video
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video> // Changé de Videos à Video
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class); // Changé de Videos::class à Video::class
    }

    // Les méthodes commentées peuvent rester telles quelles, mais ajuste les références à Videos si tu les utilises
    /**
     * @return Video[] Returns an array of Video objects
     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneBySomeField($value): ?Video // Changé de ?Videos à ?Video
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}