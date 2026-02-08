<?php

namespace App\Models\Entities;

use Dobine\Properties\Ids\Id;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "zk_screenings", indexes: [
	new ORM\Index(columns: ["movie"], name: "movie"),
	new ORM\Index(columns: ["cinema"], name: "cinema"),
	new ORM\Index(columns: ["format"], name: "format"),
	new ORM\Index(columns: ["type"], name: "type"),
	new ORM\Index(columns: ["dubbing"], name: "dubbing"),
	new ORM\Index(columns: ["subtitles"], name: "subtitles")
])]
class Screening {
	use Id;
	
	#[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: "screenings")]
	#[ORM\JoinColumn(name: "movie", referencedColumnName: "id", nullable: false)]
	private Movie $movie;
	
	#[ORM\ManyToOne(targetEntity: Cinema::class, inversedBy: "screenings")]
	#[ORM\JoinColumn(name: "cinema", referencedColumnName: "id", nullable: false)]
	private Cinema $cinema;
	
	#[ORM\ManyToOne(targetEntity: ScreeningFormat::class)]
	#[ORM\JoinColumn(name: "format", referencedColumnName: "id", nullable: true)]
	private ?ScreeningFormat $format = null;
	
	#[ORM\ManyToOne(targetEntity: ScreeningType::class)]
	#[ORM\JoinColumn(name: "type", referencedColumnName: "id", nullable: true)]
	private ?ScreeningType $type = null;
	
	#[ORM\ManyToOne(targetEntity: Place::class, inversedBy: "screenings")]
	#[ORM\JoinColumn(name: "place", referencedColumnName: "id", nullable: true)]
	private ?Place $place = null;
	
	#[ORM\Column(name: "dubbing", type: "string", length: 255, nullable: true)]
	private ?string $dubbing = null;
	
	#[ORM\Column(name: "subtitles", type: "string", length: 255, nullable: true)]
	private ?string $subtitles = null;
	
	#[ORM\Column(name: "price", type: "integer", nullable: true)]
	private ?int $price = null;
	
	#[ORM\Column(name: "link", type: "string", length: 1000, nullable: true)]
	private ?string $link = null;
	
	#[ORM\OneToMany(mappedBy: "screening", targetEntity: Showtime::class, cascade: ["persist", "remove"])]
	private Collection $showtimes;
	
	public function __construct(Cinema $cinema, Movie $movie) {
		$this->cinema = $cinema;
		$this->movie = $movie;
		$this->showtimes = new ArrayCollection();
	}
	
	public function __toString() {
		return $this->getCinema()."-".$this->getMovie()->getId()."-".$this->getType()."-".$this->getDubbing()."-".$this->getSubtitles();
	}
	
	public function getMovie(): ?Movie {
		return $this->movie;
	}
	
	public function setMovie(?Movie $movie): self {
		$this->movie = $movie;
		return $this;
	}
	
	public function getCinema(): ?Cinema {
		return $this->cinema;
	}
	
	public function setCinema(?Cinema $cinema): self {
		$this->cinema = $cinema;
		return $this;
	}
	
	public function getPrice(): ?int {
		return $this->price;
	}
	
	public function setPrice(?int $price): self {
		if(isset($price) && (!empty($price) || $price === 0)) {
			$this->price = intval($price);
		} else {
			$this->price = null;
		}
		
		return $this;
	}
	
	public function fixPrice(): ?string {
		if(!isset($this->price) || !is_numeric($this->price)) {
			return null;
		} else {
			if($this->price == 0) {
				return "zdarma";
			} else {
				return $this->price." Kč";
			}
		}
	}
	
	public function getLink(): ?string {
		return $this->link;
	}
	
	public function setLink(?string $link): self {
		$this->link = $link;
		return $this;
	}
	
	public function getDubbing(): ?string {
		return $this->dubbing;
	}
	
	public function setDubbing(?string $dubbing): self {
		$this->dubbing = $dubbing;
		return $this;
	}
	
	public function getSubtitles(): ?string {
		return $this->subtitles;
	}
	
	public function setSubtitles(?string $subtitles): self {
		$this->subtitles = $subtitles;
		return $this;
	}
	
	public function getFormat(): ?ScreeningFormat {
		return $this->format;
	}
	
	public function setFormat(?ScreeningFormat $format): self {
		$this->format = $format;
		return $this;
	}
	
	public function getType(): ?ScreeningType {
		return $this->type;
	}
	
	public function setType(?ScreeningType $type): self {
		$this->type = $type;
		return $this;
	}
	
	public function getPlace(): ?Place {
		return $this->place;
	}
	
	public function setPlace(?Place $place): self {
		$this->place = $place;
		return $this;
	}
	
	public function setLanguages(?string $dubbing, ?string $subtitles): self {
		$this->dubbing = $dubbing;
		$this->subtitles = $subtitles;
		return $this;
	}
	
	public function getShowtimes(): Collection {
		return $this->showtimes;
	}
	
	public function addShowtime(Showtime $showtime): self {
		if(!$this->showtimes->contains($showtime)) {
			$this->showtimes[] = $showtime;
			$showtime->setScreening($this);
		}
		
		return $this;
	}
	
	public function removeShowtime(Showtime $showtime): self {
		if($this->showtimes->contains($showtime)) {
			$this->showtimes->removeElement($showtime);
		}
		
		return $this;
	}
	
	public function setShowtimes(array $datetimes): self {
		foreach($datetimes as $datetime) {
			$showtime = new Showtime($this, $datetime);
			$this->addShowtime($showtime);
		}
		
		return $this;
	}
}
