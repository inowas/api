<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\DomainEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DomainEvent::class);
    }

    /**
     * @param string $aggregateId
     * @return int
     * @throws NonUniqueResultException
     */
    public function getVersion(string $aggregateId): int
    {
        $result = $this->createQueryBuilder('e')
            ->select('count(e)')
            ->where('e.aggregateId = :aggregateId')
            ->setParameter('aggregateId', $aggregateId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$result;
    }
}
