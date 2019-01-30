<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserManager
{

    private $entityManager;
    private $passwordEncoder;


    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param string $username
     * @param string $password
     * @throws \Exception
     */
    public function create(string $username, string $password): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        if ($userRepository->findOneBy(['username' => $username]) instanceof User) {
            throw new \Exception('User already exits');
        }

        $password = $this->passwordEncoder->encodePassword(new User($username, $password), $password);
        $user = new User($username, $password, ['ROLE_USER'], true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
