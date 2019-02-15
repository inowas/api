<?php

declare(strict_types=1);

namespace App\Model\SimpleTool;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Model\ToolInstance;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ApiResource(
 *     collectionOperations={"get"={"method"="GET", "path"="/tools/{tool}"}},
 *     itemOperations={"get"={"method"="GET", "path"="/tools/{tool}/{id"}},
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 *     )
 */
class SimpleTool extends ToolInstance
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

    public function toArray()
    {
        $arr = parent::toArray();
        $arr['data'] = $this->data;
        return $arr;
    }
}
