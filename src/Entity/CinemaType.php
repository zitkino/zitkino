<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "zk_cinemas_types", uniqueConstraints: [new ORM\UniqueConstraint(name: "code", columns: ["code"])])]
#[ORM\Entity]
class CinemaType
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;

    #[ORM\Column(name: "code", type: "string", length: 191, nullable: false)]
    private $code;

    #[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
    private $name;

    public function __construct(string $code)
    {
        $this->code = $code;
        $this->name = $code;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
