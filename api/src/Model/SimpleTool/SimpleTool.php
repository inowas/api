<?php

declare(strict_types=1);

namespace App\Model\SimpleTool;

use App\Model\ToolInstance;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
final class SimpleTool extends ToolInstance
{
    /**
     * @ORM\Column(name="data", type="json_array")
     */
    private $data = [];

    public function data(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}