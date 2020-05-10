<?php
namespace Zitkino\Cinemas;

use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Zitkino\Screenings\{Screening, Screenings, Showtime};

/**
 * Cinema
 *
 * @ORM\Table(name="cinemas", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"}), @ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="type", columns={"type"})})
 * @ORM\Entity(repositoryClass="Zitkino\Cinemas\CinemaRepository")
 */
class Cinema {
	use Identifier;
	
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
	 * @var bool
	 * @ORM\Column(name="parsable", type="boolean", options={"default": 0}, nullable=false)
	 */
	protected $parsable;
	
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
	 * @var \DateTime|null
	 * @ORM\Column(name="parsed", type="datetime", nullable=true)
	 */
	protected $parsed;
	
	/**
	 * @var Screenings
	 * @ORM\OneToMany(targetEntity="\Zitkino\Screenings\Screening", mappedBy="cinema", cascade={"persist", "remove"})
	 */
	private $screenings;
	
	public function __construct(string $code) {
		$this->code = $code;
		$this->name = $code;
		$this->screenings = new Screenings(null);
	}
	
	public function __toString() {
		return $this->getCode();
	}
	
	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	/**
	 * @return string
	 */
	public function getCode(): string {
		return $this->code;
	}
	
	/**
	 * @return bool
	 */
	public function isParsable(): bool {
		return $this->parsable;
	}
	
	/**
	 * @return CinemaType
	 */
	public function getType(): CinemaType {
		return $this->type;
	}
	
	/**
	 * @return string|null
	 */
	public function getAddress(): ?string {
		return $this->address;
	}
	
	/**
	 * @return string
	 */
	public function getCity(): string {
		return $this->city;
	}
	
	/**
	 * @return string|null
	 */
	public function getPhone(): ?string {
		return $this->phone;
	}
	
	/**
	 * @return string|null
	 */
	public function getEmail(): ?string {
		return $this->email;
	}
	
	/**
	 * @return string|null
	 */
	public function getUrl(): ?string {
		return $this->url;
	}
	
	/**
	 * @return string|null
	 */
	public function getGmaps(): ?string {
		return $this->gmaps;
	}
	
	/**
	 * @return string|null
	 */
	public function getProgramme(): ?string {
		return $this->programme;
	}
	
	/**
	 * @return string|null
	 */
	public function getFacebook(): ?string {
		return $this->facebook;
	}
	
	/**
	 * @return string|null
	 */
	public function getGooglePlus(): ?string {
		return $this->googlePlus;
	}
	
	/**
	 * @return string|null
	 */
	public function getInstagram(): ?string {
		return $this->instagram;
	}
	
	/**
	 * @return string|null
	 */
	public function getTwitter(): ?string {
		return $this->twitter;
	}
	
	/**
	 * @return \DateTime|null
	 */
	public function getActiveSince(): ?\DateTime {
		return $this->activeSince;
	}
	
	/**
	 * @return \DateTime|null
	 */
	public function getActiveUntil(): ?\DateTime {
		return $this->activeUntil;
	}
	
	/**
	 * @return \DateTime|null
	 */
	public function getParsed(): ?\DateTime {
		return $this->parsed;
	}
	
	/**
	 * @param \DateTime|null $parsed
	 * @return Cinema
	 */
	public function setParsed(?\DateTime $parsed): Cinema {
		$this->parsed = $parsed;
		return $this;
	}
	
	/**
	 * @param string $type
	 * @return Screenings
	 */
	public function getScreenings($type = "all"): Screenings {
		switch($type) {
			case "all":
			default:
				return $this->screenings;
				break;
			case "soonest":
				return $this->getSoonestScreenings();
				break;
			case "new":
				return $this->getNewScreenings();
				break;
		}
	}
	
	/**
	 * @param Screenings $screenings
	 * @return Cinema
	 */
	public function setScreenings(Screenings $screenings): Cinema {
		$this->screenings = $screenings;
		return $this;
	}
	
	public function addScreening(Screening $screening) {
		$this->screenings->add($screening);
	}
	
	public function hasScreenings(): bool {
		if(isset($this->screenings) and !empty($this->screenings->toArray())) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getSoonestScreenings(): Screenings {
		$soonest = [];
		if(isset($this->screenings)) {
			$currentDate = new \DateTime();
			
			/** @var Screening $screening */
			foreach($this->screenings as $screening) {
				$nextDate = new \DateTime();
				$nextDate->modify("+1 days");
				
				$showtimes = $screening->getShowtimes();
				if(isset($showtimes)) {
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
		if(isset($this->screenings)) {
			$currentDate = new \DateTime();
			
			/** @var Screening $screening */
			foreach($this->screenings as $screening) {
				$showtimes = $screening->getShowtimes();
				if(isset($showtimes)) {
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
}
