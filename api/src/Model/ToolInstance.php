<?php

declare(strict_types=1);

namespace App\Model;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="simpletools")
 *
 * @ApiResource(attributes={"access_control"="is_granted('ROLE_USER')"})
 */
final class ToolInstance
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string", unique=true, nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tool", type="string", length=255, nullable=false)
     */
    private $tool;

    /**
     * @var array
     *
     * @ORM\Column(name="metadata", type="json_array")
     */
    private $metadata = [];

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="json_array")
     */
    private $data = [];

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=36, nullable=false)
     */
    private $userId;

    public function __clone()
    {
        $this->id = null;
    }

    public static function createWith(string $id, string $tool): ToolInstance
    {
        return new self($id, $tool);
    }

    private function __construct(string $id, string $tool)
    {
        $this->id = $id;
        $this->tool = $tool;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTool(): string
    {
        return $this->tool;
    }

    /**
     * @param string $tool
     */
    public function setTool(string $tool): void
    {
        $this->tool = $tool;
    }

    /**
     * @return ToolMetadata
     */
    public function getMetadata(): ToolMetadata
    {
        return ToolMetadata::fromArray($this->metadata);
    }

    /**
     * @param ToolMetadata $metadata
     */
    public function setMetadata(ToolMetadata $metadata): void
    {
        $this->metadata = $metadata->toArray();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getName(): string
    {
        return ToolMetadata::fromArray($this->metadata)->name();
    }

    public function getDescription(): string
    {
        return ToolMetadata::fromArray($this->metadata)->description();
    }

    public function isPublic(): bool
    {
        return ToolMetadata::fromArray($this->metadata)->isPublic();
    }
}
