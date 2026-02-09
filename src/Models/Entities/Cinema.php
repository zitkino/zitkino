<?php

namespace App\Models\Entities;

use App\Models\Repositories\CinemaRepository;
use Dobine\Properties\{Ids\Id, Ids\Identable, Knp\Translatable as KnpTranslatable, Sluggable, Sortable};
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Cinema
 */
#[ORM\Entity(repositoryClass: CinemaRepository::class)]
#[ORM\Table(name: "zk_cinemas", indexes: [new ORM\Index(columns: ["type"], name: "type")])]
class Cinema implements TranslatableInterface {
	use Id, Identable, Sluggable, KnpTranslatable, Sortable;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
	public string $name {
		get => $this->name;
		set => $this->name = $value;
	}
	
	#[ORM\ManyToOne(targetEntity: "CinemaType", inversedBy: "cinemas")]
	#[ORM\JoinColumn(name: "type", referencedColumnName: "id", nullable: true)]
	public ?CinemaType $type = null {
		get => $this->type;
		set => $this->type = $value;
	}
	
	#[ORM\Column(name: "address", type: "string", length: 255, nullable: true)]
	public ?string $address = null {
		get => $this->address;
		set => $this->address = $value;
	}
	
	#[ORM\Column(name: "city", type: "string", length: 255, nullable: false, options: ["default" => "Brno"])]
	public string $city = "Brno" {
		get => $this->city;
		set => $this->city = $value;
	}
	
	#[ORM\Column(name: "phone", type: "string", length: 100, nullable: true)]
	public ?string $phone = null {
		get => $this->phone;
		set => $this->phone = $value;
	}
	
	#[ORM\Column(name: "email", type: "string", length: 255, nullable: true)]
	public ?string $email = null {
		get => $this->email;
		set => $this->email = $value;
	}
	
	#[ORM\Column(name: "url", type: "string", length: 1000, nullable: true)]
	public ?string $url = null {
		get => $this->url;
		set => $this->url = $value;
	}
	
	#[ORM\Column(name: "gmaps", type: "string", length: 1000, nullable: true)]
	public ?string $gmaps = null {
		get => $this->gmaps;
		set => $this->gmaps = $value;
	}
	
	#[ORM\Column(name: "programme", type: "string", length: 255, nullable: true)]
	public ?string $programme = null {
		get => $this->programme;
		set => $this->programme = $value;
	}
	
	#[ORM\Column(name: "facebook", type: "string", length: 255, nullable: true)]
	public ?string $facebook = null {
		get => $this->facebook;
		set => $this->facebook = $value;
	}
	
	#[ORM\Column(name: "googlePlus", type: "string", length: 255, nullable: true)]
	public ?string $googlePlus = null {
		get => $this->googlePlus;
		set => $this->googlePlus = $value;
	}
	
	#[ORM\Column(name: "instagram", type: "string", length: 255, nullable: true)]
	public ?string $instagram = null {
		get => $this->instagram;
		set => $this->instagram = $value;
	}
	
	#[ORM\Column(name: "twitter", type: "string", length: 255, nullable: true)]
	public ?string $twitter = null {
		get => $this->twitter;
		set => $this->twitter = $value;
	}
	
	#[ORM\Column(name: "active_since", type: "date", nullable: true)]
	public ?\DateTime $activeSince = null {
		get => $this->activeSince;
		set => $this->activeSince = $value;
	}
	
	#[ORM\Column(name: "active_until", type: "date", nullable: true)]
	public ?\DateTime $activeUntil = null {
		get => $this->activeUntil;
		set => $this->activeUntil = $value;
	}
	
	#[ORM\Column(name: "parsable", type: "boolean", nullable: false, options: ["default" => 0])]
	public bool $parsable = false {
		get => $this->parsable;
		set => $this->parsable = $value;
	}
	
	#[ORM\Column(name: "parsing", type: "string", length: 255, nullable: true)]
	public ?string $parsing = null {
		get => $this->parsing;
		set => $this->parsing = $value;
	}
	
	#[ORM\Column(name: "parsed", type: "datetime", nullable: true)]
	public ?\DateTime $parsed = null {
		get => $this->parsed;
		set => $this->parsed = $value;
	}
	
	#[ORM\OneToMany(mappedBy: "cinema", targetEntity: "Screening", cascade: ["persist", "remove"])]
	public Collection $screenings {
		get => $this->screenings;
		set => $this->screenings = $value;
	}
	
	#[ORM\OneToMany(mappedBy: "cinema", targetEntity: "Place")]
	public Collection $places {
		get => $this->places;
	}
	
	public function __construct(string $ident = '') {
		if(!empty($ident)) {
			$this->ident = $ident;
			$this->name = $ident;
		}
		
		$this->screenings = new ArrayCollection();
		$this->places = new ArrayCollection();
	}
	
	public function __toString() {
		return $this->name;
	}
	
	public function getScreenings(string $type = "all"): Screenings {
		switch($type) {
			case "all":
			default:
				return $this->screenings;
			case "soonest":
				return $this->getSoonestScreenings();
			case "new":
				return $this->getNewScreenings();
		}
	}
	
	public function setScreenings(Screenings $screenings): Cinema {
		$this->screenings = $screenings;
		return $this;
	}
	
	public function addScreening(Screening $screening): void {
		$this->screenings->add($screening);
	}
	
	public function hasScreenings(): bool {
		if(!empty($this->screenings->toArray())) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getSoonestScreenings(): Screenings {
		$soonest = [];
		if(!$this->screenings->isEmpty()) {
			$currentDate = new \DateTime();
			
			/** @var Screening $screening */
			foreach($this->screenings as $screening) {
				$nextDate = new \DateTime();
				$nextDate->modify("+1 days");
				
				$showtimes = $screening->showtimes;
				if(!$showtimes->isEmpty()) {
					/** @var Showtime $showtime */
					foreach($showtimes as $showtime) {
						// checks if movie is played from now to +1 day
						if($currentDate < $showtime->datetime and $showtime->datetime < $nextDate) {
							$soonest[] = $screening;
							break;
						}
					}
				}
			}
			
			if(count($soonest) < 5) {
				$soonest = [];
				for($i = 0; $i < count($this->screenings->toArray()); $i++) {
					if(isset($this->screenings[$i])) {
						foreach($this->screenings[$i]->showtimes as $showtime) {
							if($currentDate < $showtime->datetime) {
								$soonest[] = $this->screenings[$i];
							}
						}
					}
					
					if(count($soonest) == 5) {
						break;
					}
				}
			}
		}
		
		return new Screenings($soonest);
	}
	
	public function getNewScreenings(): Screenings {
		$new = [];
		if(!$this->screenings->isEmpty()) {
			$currentDate = new \DateTime();
			
			/** @var Screening $screening */
			foreach($this->screenings as $screening) {
				$showtimes = $screening->showtimes;
				if(!$showtimes->isEmpty()) {
					/** @var Showtime $showtime */
					foreach($showtimes as $showtime) {
						if($currentDate < $showtime->datetime) {
							$new[] = $screening;
							break;
						}
					}
				}
			}
		}
		return new Screenings($new);
	}
	
	public function addPlace(Place $place): self {
		if(!$this->places->contains($place)) {
			$this->places[] = $place;
			$place->cinema = $this;
		}
		
		return $this;
	}
	
	public function removePlace(Place $place): self {
		if($this->places->removeElement($place)) {
			// set the owning side to null (unless already changed)
			if($place->cinema === $this) {
				$place->cinema = null;
			}
		}
		
		return $this;
	}
}
