<?php

// src/Repository/ArticleRepository.php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Recherche des articles par mot-clé dans le titre ou le contenu.
     *
     * @param string|null $keyword
     * @return Article[]
     */
    public function searchArticles(?string $keyword): array
    {
        $qb = $this->createQueryBuilder('a');

        if (!empty($keyword)) {
            $qb->andWhere('a.titre LIKE :keyword OR a.contenu LIKE :keyword')
               ->setParameter('keyword', '%' . $keyword . '%');
        }

        return $qb->orderBy('a.id', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    /**
 * Récupère tous les articles triés par date de création décroissante.
 *
 * @return Article[]
 */
public function findAllOrderedByDateDesc(): array
{
    return $this->createQueryBuilder('a')
        ->orderBy('a.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
}
}