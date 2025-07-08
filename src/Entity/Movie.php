<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Movie
 *
 * @ORM\Table(name="zk_movies", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity(repositoryClass=\App\Repository\MovieRepository::class)
 */
class Movie
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
     * @ORM\Column(name="name", type="string", length=191, nullable=false)
     */
    private $name;

    /**
     * @var int|null
     * @ORM\Column(name="length", type="integer", nullable=true)
     */
    private $length;

    /**
     * @var string|null
     * @ORM\Column(name="csfd", type="string", length=255, nullable=true)
     */
    private $csfd;

    /**
     * @var string|null
     * @ORM\Column(name="imdb", type="string", length=255, nullable=true)
     */
    private $imdb;

    /** @var array */
    private $databases;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Screening::class, mappedBy="movie", cascade={"persist", "remove"})
     */
    private $screenings;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->setDatabases();
        $this->screenings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Screening[]
     */
    public function getScreenings(): Collection
    {
        return $this->screenings;
    }

    public function addScreening(Screening $screening): self
    {
        if (!$this->screenings->contains($screening)) {
            $this->screenings[] = $screening;
            $screening->setMovie($this);
        }

        return $this;
    }

    public function removeScreening(Screening $screening): self
    {
        if ($this->screenings->removeElement($screening)) {
            // set the owning side to null (unless already changed)
            if ($screening->getMovie() === $this) {
                $screening->setMovie(null);
            }
        }

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function getCsfd(): ?string
    {
        return $this->csfd;
    }

    public function setCsfd(?string $csfd): self
    {
        $this->csfd = $csfd;
        return $this;
    }

    public function getImdb(): ?string
    {
        return $this->imdb;
    }

    public function setImdb(?string $imdb): self
    {
        $this->imdb = $imdb;
        return $this;
    }

    public function getDatabases(): ?array
    {
        $this->setDatabases();
        return $this->databases;
    }

    public function setDatabases(): void
    {
        $csfdUrl = "https://www.csfd.cz";
        if (isset($this->csfd)) {
            $this->databases["csfd"] = $csfdUrl . "/film/" . $this->csfd;
        } else {
            $this->databases["csfd"] = $csfdUrl . "/hledat/?q=" . urlencode($this->name);
        }

        $imdbUrl = "https://www.imdb.com";
        if (isset($this->imdb)) {
            $this->databases["imdb"] = $imdbUrl . "/title/" . $this->imdb;
        } else {
            $this->databases["imdb"] = $imdbUrl . "/find?s=tt&q=" . urlencode($this->name);
        }
    }
}
