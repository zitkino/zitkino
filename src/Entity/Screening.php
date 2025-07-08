<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "zk_screenings", indexes: [
    new ORM\Index(name: "movie", columns: ["movie"]), 
    new ORM\Index(name: "cinema", columns: ["cinema"]), 
    new ORM\Index(name: "type", columns: ["type"]), 
    new ORM\Index(name: "dubbing", columns: ["dubbing"]), 
    new ORM\Index(name: "subtitles", columns: ["subtitles"])
])]
#[ORM\Entity(repositoryClass: \App\Repository\ScreeningRepository::class)]
class Screening
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;

    #[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: "screenings")]
    #[ORM\JoinColumn(name: "movie", referencedColumnName: "id", nullable: false)]
    private $movie;

    #[ORM\ManyToOne(targetEntity: Cinema::class, inversedBy: "screenings")]
    #[ORM\JoinColumn(name: "cinema", referencedColumnName: "id", nullable: false)]
    private $cinema;

    #[ORM\ManyToOne(targetEntity: ScreeningType::class)]
    #[ORM\JoinColumn(name: "type", referencedColumnName: "id", nullable: true)]
    private $type;

    #[ORM\ManyToOne(targetEntity: Place::class, inversedBy: "screenings")]
    #[ORM\JoinColumn(name: "place", referencedColumnName: "id", nullable: true)]
    private $place;

    #[ORM\Column(name: "dubbing", type: "string", length: 255, nullable: true)]
    private $dubbing;

    #[ORM\Column(name: "subtitles", type: "string", length: 255, nullable: true)]
    private $subtitles;

    #[ORM\Column(name: "price", type: "integer", nullable: true)]
    private $price;

    #[ORM\Column(name: "link", type: "string", length: 1000, nullable: true)]
    private $link;

    #[ORM\OneToMany(targetEntity: Showtime::class, mappedBy: "screening", cascade: ["persist", "remove"])]
    private $showtimes;

    public function __construct(Movie $movie, Cinema $cinema)
    {
        $this->movie = $movie;
        $this->cinema = $cinema;
        $this->showtimes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getMovie()->getId() . "-" . $this->getCinema() . "-" . $this->getType() . "-" . $this->getDubbing() . "-" . $this->getSubtitles();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;
        return $this;
    }

    public function getCinema(): ?Cinema
    {
        return $this->cinema;
    }

    public function setCinema(?Cinema $cinema): self
    {
        $this->cinema = $cinema;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        if (isset($price) && (!empty($price) || $price === 0)) {
            $this->price = intval($price);
        } else {
            $this->price = null;
        }

        return $this;
    }

    public function fixPrice(): ?string
    {
        if (!isset($this->price) || !is_numeric($this->price)) {
            return null;
        } else {
            if ($this->price == 0) {
                return "zdarma";
            } else {
                return $this->price . " Kč";
            }
        }
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function getDubbing(): ?string
    {
        return $this->dubbing;
    }

    public function setDubbing(?string $dubbing): self
    {
        $this->dubbing = $dubbing;
        return $this;
    }

    public function getSubtitles(): ?string
    {
        return $this->subtitles;
    }

    public function setSubtitles(?string $subtitles): self
    {
        $this->subtitles = $subtitles;
        return $this;
    }

    public function getType(): ?ScreeningType
    {
        return $this->type;
    }

    public function setType(?ScreeningType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;
        return $this;
    }

    public function setLanguages(?string $dubbing, ?string $subtitles): self
    {
        $this->dubbing = $dubbing;
        $this->subtitles = $subtitles;
        return $this;
    }

    /**
     * @return Collection|Showtime[]
     */
    public function getShowtimes(): Collection
    {
        return $this->showtimes;
    }

    public function addShowtime(Showtime $showtime): self
    {
        if (!$this->showtimes->contains($showtime)) {
            $this->showtimes[] = $showtime;
            $showtime->setScreening($this);
        }

        return $this;
    }

    public function removeShowtime(Showtime $showtime): self
    {
        if ($this->showtimes->removeElement($showtime)) {
            // set the owning side to null (unless already changed)
            if ($showtime->getScreening() === $this) {
                $showtime->setScreening(null);
            }
        }

        return $this;
    }

    public function setShowtimes(array $datetimes, bool $actual = true): void
    {
        foreach ($datetimes as $datetime) {
            $showtime = new Showtime($this, $datetime);

            if ($actual === true) {
                if ($showtime->isActual()) {
                    $this->addShowtime($showtime);
                }
            } else {
                if ($actual === false) {
                    $this->addShowtime($showtime);
                }
            }
        }
    }
}
