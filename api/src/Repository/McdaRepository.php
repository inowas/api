<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Mcda\Mcda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class McdaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mcda::class);
    }
}
