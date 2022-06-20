<?php
namespace Zitkino\Cinemas;

use Dobine\Attributes\Id;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Zitkino\Screenings\{Screening, Screenings, Showtime};

/**
 * Cinema
 *
 * @ORM\Table(name="cinemas", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"}), @ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="type", columns={"type"})})
 * @ORM\Entity(repositoryClass="Zitkino\Cinemas\CinemaRepository")
 */
class Cinema {
	use Id;
	
	/**
	 * @var string
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	protected $name;
	
	/**
	 * @var string
	 * @ORM\Column(name="code", type="string", length=20, nullable=false)
	 */
	protected $code;
	
	/**
	 * @var CinemaType
	 * @ORM\ManyToOne(targetEntity="CinemaType")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=true)
	 * })
	 */
	protected $type;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="address", type="string", length=255, nullable=true)
	 */
	protected $address;
	
	/**
	 * @var string
	 * @ORM\Column(name="city", type="string", length=255, nullable=false, options={"default"="Brno"})
	 */
	protected $city = 'Brno';
	
	/**
	 * @var string|null
	 * @ORM\Column(name="phone", type="string", length=100, nullable=true)
	 */
	protected $phone;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="email", type="string", length=255, nullable=true)
	 */
	protected $email;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="url", type="string", length=1000, nullable=true)
	 */
	protected $url;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="gmaps", type="string", length=1000, nullable=true)
	 */
	protected $gmaps;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="programme", type="string", length=255, nullable=true)
	 */
	protected $programme;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="facebook", type="string", length=255, nullable=true)
	 */
	protected $facebook;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="googlePlus", type="string", length=255, nullable=true)
	 */
	protected $googlePlus;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="instagram", type="string", length=255, nullable=true)
	 */
	protected $instagram;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="twitter", type="string", length=255, nullable=true)
	 */
	protected $twitter;
	
	/**
	 * @var \DateTime|null
	 * @ORM\Column(name="active_since", type="date", nullable=true)
	 */
	protected $activeSince;
	
	/**
	 * @var \DateTime|null
	 * @ORM\Column(name="active_until", type="date", nullable=true)
	 */
	protected $activeUntil;
	
	/**
	 * @var bool
	 * @ORM\Column(name="parsable", type="boolean", options={"default": 0}, nullable=false)
	 */
	protected $parsable;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="parsing", type="string", length=255, nullable=true)
	 */
	protected $parsing;
	
	/**
	 * @var \DateTime|null
	 * @ORM\Column(name="parsed", type="datetime", nullable=true)
	 */
	protected $parsed;
	
	/**
	 * @var Screenings
	 * @ORM\OneToMany(targetEntity="\Zitkino\Screenings\Screening", mappedBy="cinema", cascade={"persist", "remove"})
	 */
	private $screenings;
	
	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="\Zitkino\Place", mappedBy="cinema")
	 */
	private $places;
	
	public function __construct(string $code) {
		$this->code = $code;
		$this->name = $code;
		$this->screenings = new Screenings([]);
		$this->places = new ArrayCollection();
	}
	
	public function __toString() {
		return $this->getCode();
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function getCode(): string {
		return $this->code;
	}
	
	public function getType(): CinemaType {
		return $this->type;
	}
	
	public function getAddress(): ?string {
		return $this->address;
	}
	
	public function getCity(): string {
		return $this->city;
	}
	
	public function getPhone(): ?string {
		return $this->phone;
	}
	
	public function getEmail(): ?string {
		return $this->email;
	}
	
	public function getUrl(): ?string {
		return $this->url;
	}
	
	public function getGmaps(): ?string {
		return $this->gmaps;
	}
	
	public function getProgramme(): ?string {
		return $this->programme;
	}
	
	public function getFacebook(): ?string {
		return $this->facebook;
	}
	
	public function getGooglePlus(): ?string {
		return $this->googlePlus;
	}
	
	public function getInstagram(): ?string {
		return $this->instagram;
	}
	
	public function getTwitter(): ?string {
		return $this->twitter;
	}
	
	public function getActiveSince(): ?\DateTime {
		return $this->activeSince;
	}
	
	public function getActiveUntil(): ?\DateTime {
		return $this->activeUntil;
	}
	
	public function isParsable(): bool {
		return $this->parsable;
	}
	
	public function getParsing(): ?string {
		return $this->parsing;
	}
	
	public function setParsing(?string $parsing): Cinema {
		$this->parsing = $parsing;
		return $this;
	}
	
	public function getParsed(): ?\DateTime {
		return $this->parsed;
	}
	
	public function setParsed(?\DateTime $parsed): Cinema {
		$this->parsed = $parsed;
		return $this;
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
				
				$showtimes = $screening->getShowtimes();
				if(!$showtimes->isEmpty()) {
					/** @var Showtime $showtime */
					foreach($showtimes as $showtime) {
						// checks if movie is played from now to +1 day
						if($currentDate < $showtime->getDatetime() and $showtime->getDatetime() < $nextDate) {
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
						foreach($this->screenings[$i]->getShowtimes() as $showtime) {
							if($currentDate < $showtime->getDatetime()) {
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

//		if(empty($soonest)) {
//			if(is_null($this->screenings) or empty($this->screenings->toArray())) {
//				$soonest = [];
//			} else {
//				if($this->screenings[0]->getShowtimes()[0]->isActual()) {
//					$soonest = [$this->screenings[0]];
//				} else {
//					$soonest = [];
//				}
//			}
//		}
		
		return new Screenings($soonest);
	}
	
	public function getNewScreenings(): Screenings {
		$new = [];
		if(!$this->screenings->isEmpty())  {
			$currentDate = new \DateTime();
			
			/** @var Screening $screening */
			foreach($this->screenings as $screening) {
				$showtimes = $screening->getShowtimes();
				if(!$showtimes->isEmpty()) {
					/** @var Showtime $showtime */
					foreach($showtimes as $showtime) {
						if($currentDate < $showtime->getDatetime()) {
							$new[] = $screening;
							break;
						}
					}
				}
			}
		}
		return new Screenings($new);
	}
	
	public function getPlaces(): Collection {
		return $this->places;
	}
	
	public function setPlaces(Collection $places): Cinema {
		$this->places = $places;
		return $this;
	}
}
