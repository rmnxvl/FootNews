<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Joueur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Joueur>
 */
class JoueurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Joueur::class);
    }

    /**
     * Recherche des joueurs par nom complet.
     *
     * @param string|null $keyword
     * @return Joueur[]
     */
    public function searchPlayers(?string $keyword): array
    {
        $qb = $this->createQueryBuilder('j');

        if (!empty($keyword)) {
            $qb->andWhere('j.nomComplet LIKE :keyword')
               ->setParameter('keyword', '%' . $keyword . '%');
        }

        return $qb->orderBy('j.nomComplet', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}