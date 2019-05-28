<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Modflow\ModflowModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ModflowModelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ModflowModel::class);
    }
}
