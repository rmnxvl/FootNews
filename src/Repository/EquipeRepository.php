<?php

namespace App\Repository;

use App\Entity\Equipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipe>
 */
class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

    /**
     * Rechercher des équipes par mot-clé dans le nom ou le pays.
     *
     * @param string|null $keyword
     * @return Equipe[]
     */
    public function searchTeams(?string $keyword): array
    {
        $qb = $this->createQueryBuilder('e'); // Alias pour l'entité `Equipe`

        if (!empty($keyword)) {
            $qb->andWhere('e.nom LIKE :keyword OR e.pays LIKE :keyword') // Utilise les bons champs
               ->setParameter('keyword', '%' . $keyword . '%');
        }

        return $qb->orderBy('e.id', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
}