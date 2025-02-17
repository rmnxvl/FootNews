<?php

namespace App\Repository;

use App\Entity\ResetPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResetPassword>
 */
class ResetPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPassword::class);
    }

    /**
     * Trouve un token actif pour un utilisateur donné
     */
    public function findValidToken(string $email): ?ResetPassword
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.email = :email')
            ->andWhere('r.expiresAt > :now')
            ->setParameter('email', $email)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
