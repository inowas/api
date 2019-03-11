<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\SimpleTool\SimpleTool;
use App\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SimpleToolRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SimpleTool::class);
    }

    /**
     * @param string $tool
     * @param User $user
     * @param bool $isPublic
     * @return array
     */
    public function getTool(string $tool, User $user, bool $isPublic): array
    {

        if ($isPublic) {
            return $this->createQueryBuilder('t')
                ->andWhere('t.tool LIKE :tool')
                ->andWhere('t.isPublic = :isPublic')
                ->setParameter('tool', $tool.'%')
                ->setParameter('isPublic', $isPublic)
                ->getQuery()
                ->getResult();
        }

        return $this->createQueryBuilder('t')
            ->andWhere('t.tool LIKE :tool')
            ->andWhere('t.user = :user')
            ->setParameter('tool', $tool.'%')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
