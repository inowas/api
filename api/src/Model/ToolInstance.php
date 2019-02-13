<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class ToolInstance
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="string", unique=true, nullable=false)
     */
    protected $id;

    /**
     * @ORM\Column(name="user_id", type="string", length=36, nullable=false)
     */
    protected $userId;

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
     * @ORM\Column(name="public", type="boolean", nullable=false)
     */
    protected $public;

    /**
     * @ORM\Column(name="archived", type="boolean", nullable=false)
     */
    protected $archived;

    public function __clone()
    {
        $this->id = null;
    }

    public static function createWithParams(string $id, string $userId, string $tool, ToolMetadata $metadata)
    {
        $static = new static();
        $static->id = $id;
        $static->userId = $userId;
        $static->tool = $tool;
        $static->name = $metadata->name();
        $static->description = $metadata->description();
        $static->public = $metadata->isPublic();
        $static->archived = false;
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
        return ToolMetadata::fromParams($this->name, $this->description, $this->public);
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function setMetadata(ToolMetadata $metadata): void
    {
        $this->name = $metadata->name();
        $this->description = $metadata->description();
        $this->public = $metadata->isPublic();
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
        return $this->public;
    }

    public function isArchived()
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }
}
