<?php

namespace App\Entity;

use App\Repository\LibrariesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LibrariesRepository::class)]
class Libraries
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $collection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function setCollection($collection): self
    {
        $this->collection = $collection;

        return $this;
    }
}
