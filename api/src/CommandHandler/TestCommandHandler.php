<?php

declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\TestCommand;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TestCommandHandler
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param TestCommand $command
     * @throws \Exception
     */
    public function __invoke(TestCommand $command)
    {
        #var_dump($command);
    }
}
