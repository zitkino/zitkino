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
	public Movie $movie {
		get => $this->movie;
		set => $this->movie = $value;
	}
	
	#[ORM\ManyToOne(targetEntity: Cinema::class, inversedBy: "screenings")]
	#[ORM\JoinColumn(name: "cinema", referencedColumnName: "id", nullable: false)]
	public Cinema $cinema {
		get => $this->cinema;
		set => $this->cinema = $value;
	}
	
	#[ORM\ManyToOne(targetEntity: ScreeningFormat::class)]
	#[ORM\JoinColumn(name: "format", referencedColumnName: "id", nullable: true)]
	public ?ScreeningFormat $format = null {
		get => $this->format;
		set => $this->format = $value;
	}
	
	#[ORM\ManyToOne(targetEntity: ScreeningType::class)]
	#[ORM\JoinColumn(name: "type", referencedColumnName: "id", nullable: true)]
	public ?ScreeningType $type = null {
		get => $this->type;
		set => $this->type = $value;
	}
	
	#[ORM\ManyToOne(targetEntity: Place::class, inversedBy: "screenings")]
	#[ORM\JoinColumn(name: "place", referencedColumnName: "id", nullable: true)]
	public ?Place $place = null {
		get => $this->place;
		set => $this->place = $value;
	}
	
	#[ORM\Column(name: "dubbing", type: "string", length: 255, nullable: true)]
	public ?string $dubbing = null {
		get => $this->dubbing;
		set => $this->dubbing = $value;
	}
	
	#[ORM\Column(name: "subtitles", type: "string", length: 255, nullable: true)]
	public ?string $subtitles = null {
		get => $this->subtitles;
		set => $this->subtitles = $value;
	}
	
	#[ORM\Column(name: "price", type: "integer", nullable: true)]
	public ?int $price = null {
		get => $this->price;
		set {
			if(isset($value) && (!empty($value) || $value === 0)) {
				$this->price = intval($value);
			} else {
				$this->price = null;
			}
		}
	}
	
	#[ORM\Column(name: "link", type: "string", length: 1000, nullable: true)]
	public ?string $link = null {
		get => $this->link;
		set => $this->link = $value;
	}
	
	#[ORM\OneToMany(mappedBy: "screening", targetEntity: Showtime::class, cascade: ["persist", "remove"])]
	public Collection $showtimes {
		get => $this->showtimes;
	}
	
	public function __construct(Cinema $cinema, Movie $movie) {
		$this->cinema = $cinema;
		$this->movie = $movie;
		$this->showtimes = new ArrayCollection();
	}
	
	public function __toString() {
		return $this->cinema . "-" . $this->movie->getId() . "-" . $this->type . "-" . $this->dubbing . "-" . $this->subtitles;
	}
	
	public function fixPrice(): ?string {
		if(!isset($this->price) || !is_numeric($this->price)) {
			return null;
		} else {
			if($this->price == 0) {
				return "zdarma";
			} else {
				return $this->price . " Kč";
			}
		}
	}
	
	public function setLanguages(?string $dubbing, ?string $subtitles): self {
		$this->dubbing = $dubbing;
		$this->subtitles = $subtitles;
		return $this;
	}
	
	public function addShowtime(Showtime $showtime): self {
		if(!$this->showtimes->contains($showtime)) {
			$this->showtimes[] = $showtime;
			$showtime->screening = $this;
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
