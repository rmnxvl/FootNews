<?php

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
     * Recherche des articles par mot-clé dans le titre.
     *
     * @param string|null $keyword
     * @param string|null $categorie
     * @return Article[]
     */
    public function searchArticles(?string $keyword, ?string $categorie): array
    {
        $qb = $this->createQueryBuilder('a');

        if (!empty($keyword)) {
            $qb->andWhere('a.titre LIKE :keyword OR a.contenu LIKE :keyword OR a.categorie LIKE :keyword')
               ->setParameter('keyword', '%' . $keyword . '%');
        }

        if (!empty($categorie)) {
            $qb->andWhere('a.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }

        return $qb->orderBy('a.id', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}