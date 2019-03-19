<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks
 */
abstract class ToolInstance implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="string", unique=true, nullable=false)
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(name="tool", type="string", length=36, nullable=false)
     */
    protected $tool;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    protected $description;

    /**
     * @ORM\Column(name="is_public", type="boolean", nullable=false)
     */
    protected $isPublic;

    /**
     * @ORM\Column(name="is_archived", type="boolean", nullable=false)
     */
    protected $isArchived;

    /**
     * @ORM\Column(name="is_scenario", type="boolean", nullable=false)
     */
    protected $isScenario;

    /**
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=false)
     */
    protected $updatedAt;

    public function __clone()
    {
        $this->id = null;
        $this->createdAt = null;
        $this->name .= ' (clone)';
    }

    public static function createWithParams(string $id, User $user, string $tool, ToolMetadata $metadata)
    {
        $static = new static();
        $static->id = $id;
        $static->user = $user;
        $static->tool = $tool;
        $static->name = $metadata->name();
        $static->description = $metadata->description();
        $static->isPublic = $metadata->isPublic();
        $static->isArchived = false;
        $static->isScenario = false;
        return $static;
    }

    protected function __construct()
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function tool(): string
    {
        return $this->tool;
    }

    public function metadata(): ToolMetadata
    {
        return ToolMetadata::fromParams($this->name, $this->description, $this->isPublic);
    }

    public function userId(): string
    {
        return $this->getUser()->getId()->toString();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function setMetadata(ToolMetadata $metadata): void
    {
        $this->name = $metadata->name();
        $this->description = $metadata->description();
        $this->isPublic = $metadata->isPublic();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function isArchived()
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): void
    {
        $this->isArchived = $isArchived;
    }

    public function isScenario()
    {
        return $this->isScenario;
    }

    public function setIsScenario(bool $isScenario): void
    {
        $this->isScenario = $isScenario;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    abstract public function data(): array;

    abstract public function setData(array $data): void;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @throws \Exception
     */
    public function updateTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
        if ($this->createdAt === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'userId' => $this->getUser()->getId()->toString(),
            'tool' => $this->tool,
            'name' => $this->name,
            'description' => $this->description,
            'isPublic' => $this->isPublic,
            'isArchived' => $this->isArchived,
            'isScenario' => $this->isScenario,
            'createdAt' => $this->getCreatedAt()->format(DATE_ATOM),
            'updatedAt' => $this->getUpdatedAt()->format(DATE_ATOM)
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
