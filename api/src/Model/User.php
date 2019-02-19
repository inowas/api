<?php

declare(strict_types=1);

namespace App\Model;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 *     )
 */
class User implements UserInterface
{
    /**
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="archived", type="boolean", nullable=false)
     */
    private $archived;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="json_array", nullable=false)
     */
    private $roles;

    /**
     * @var array
     *
     * @ORM\Column(name="profile", type="json_array", nullable=false)
     */
    private $profile;

    /**
     * @param string $aggregateId
     * @param string $username
     * @param string $password
     * @return User
     * @throws \Exception
     */
    public static function withAggregateId(string $aggregateId, string $username, string $password) {
        $self = new self($username, $password);
        $self->id = Uuid::fromString($aggregateId);
        return $self;
    }

    /**
     * User constructor.
     * @param string|null $username
     * @param string|null $password
     * @param array $roles
     * @param bool $enabled
     * @throws \Exception
     */
    public function __construct(string $username, string $password, array $roles = [], bool $enabled = true)
    {
        $this->id = Uuid::uuid4();
        $this->username = $username;
        $this->password = $password;
        $this->enabled = $enabled;
        $this->archived = false;
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

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getProfile(): array
    {
        return $this->profile;
    }

    public function setProfile(array $profile): void
    {
        $this->profile = $profile;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function setName(string $name): void
    {
        $this->profile['name'] = $name;
    }

    public function setEmail(string $email): void
    {
        $this->profile['email'] = $email;
    }
}
