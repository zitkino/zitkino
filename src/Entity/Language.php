<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language
 *
 * @ORM\Table(name="zk_languages", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity(repositoryClass=\App\Repository\LanguageRepository::class)
 */
class Language
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=10, nullable=false)
     */
    private $code;

    /**
     * @var string|null
     * @ORM\Column(name="czech", type="string", length=10, nullable=true)
     */
    private $czech;

    /**
     * @var string|null
     * @ORM\Column(name="english", type="string", length=10, nullable=true)
     */
    private $english;

    public function __construct(string $code)
    {
        $this->code = $code;
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

    public function getCzech(): ?string
    {
        return $this->czech;
    }

    public function setCzech(?string $czech): self
    {
        $this->czech = $czech;
        return $this;
    }

    public function getEnglish(): ?string
    {
        return $this->english;
    }

    public function setEnglish(?string $english): self
    {
        $this->english = $english;
        return $this;
    }
}
