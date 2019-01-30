<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
final class User implements UserInterface
{
    /**
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="json_array")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="profile", type="json_array")
     */
    private $profile;

    /**
     * User constructor.
     * @param string|null $username
     * @param string|null $password
     * @param array $roles
     * @param bool $enabled
     * @throws \Exception
     */
    public function __construct(?string $username, ?string $password, array $roles = [], bool $enabled = true)
    {
        if ('' === $username || null === $username) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        if ('' === $password || null === $password) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->id = Uuid::uuid4();
        $this->username = $username;
        $this->password = $password;
        $this->enabled = $enabled;
        $this->roles = $roles;
        $this->profile = [];
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getProfile(): array
    {
        return $this->profile;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function eraseCredentials()
    {
        return null;
    }
}
