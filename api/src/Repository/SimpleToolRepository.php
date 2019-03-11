<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\SimpleTool\SimpleTool;
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
     * @param bool $isPublic
     * @return array
     */
    public function getTool(string $tool, bool $isPublic): array
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


        return $result;
    }
}
