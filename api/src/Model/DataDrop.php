<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_drops")
 */
class DataDrop
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="hash", type="string", length=40, unique=true, nullable=false)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=64, unique=true, nullable=false)
     */
    private $filename;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="adapter", type="string", length=64, nullable=false)
     */
    private $adapter;

    /**
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    protected $createdAt;

    /**
     * User constructor.
     * @param string $hash
     * @param User $user
     * @param string $filename
     * @param string $adapter
     * @throws \Exception
     */
    public function __construct(string $hash, User $user, string $filename, string $adapter)
    {
        $this->hash = $hash;
        $this->user = $user;
        $this->filename = $filename;
        $this->adapter = $adapter;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function filename(): string
    {
        return $this->filename;
    }
}
