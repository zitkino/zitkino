<?php
namespace Zitkino\Cinemas;

use Dobine\Entities\DobineEntity;
use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Tracy\Debugger;
use Zitkino\Parsers\Parser;
use Zitkino\Screenings\Screenings;
use Zitkino\Screenings\Showtime;

/**
 * Cinema
 *
 * @ORM\Table(name="cinemas", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"}), @ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="type", columns={"type"})})
 * @ORM\Entity
 */
class Cinema extends DobineEntity {
	use Identifier, MagicAccessors;
	
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
     * @ORM\Column(name="disabled", type="boolean", options={"default": 0}, nullable=false)
     */
    protected $disabled;
	
	/**
	 * @var CinemaType
	 * @ORM\ManyToOne(targetEntity="CinemaType")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type", referencedColumnName="id")
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
	
	/** @var Screenings */
	protected $screenings;
	
	
	public function __construct(string $code) {
		$this->code = $code;
		$this->name = $code;
		$this->screenings = new Screenings(null);
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
	public function isDisabled(): bool {
		return $this->disabled;
	}
	
	/**
	 * @return CinemaType
	 */
	public function getType(): CinemaType {
		return $this->type;
	}
	
	/**
	 * @return null|string
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
	 * @return null|string
	 */
	public function getPhone(): ?string {
		return $this->phone;
	}
	
	/**
	 * @return null|string
	 */
	public function getEmail(): ?string {
		return $this->email;
	}
	
	/**
	 * @return null|string
	 */
	public function getUrl(): ?string {
		return $this->url;
	}
	
	/**
	 * @return null|string
	 */
	public function getGmaps(): ?string {
		return $this->gmaps;
	}
	
	/**
	 * @return null|string
	 */
	public function getProgramme(): ?string {
		return $this->programme;
	}
	
	/**
	 * @return null|string
	 */
	public function getFacebook(): ?string {
		return $this->facebook;
	}
	
	/**
	 * @return null|string
	 */
	public function getGooglePlus(): ?string {
		return $this->googlePlus;
	}
	
	/**
	 * @return null|string
	 */
	public function getInstagram(): ?string {
		return $this->instagram;
	}
	
	/**
	 * @return null|string
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
	 * @param string $type
	 * @return Screenings
	 */
	public function getScreenings($type = "all"): Screenings {
		switch($type) {
			case "all": default:
				return $this->screenings; break;
			case "soonest":
				return $this->getSoonestScreenings(); break;
			case "new":
				return $this->getNewScreenings(); break;
		}
	}
	
	public function setScreenings() {
		try {
			$parserClass = "\Zitkino\Parsers\\".ucfirst($this->code);
			if(class_exists($parserClass)) {
				/** @var Parser $parser */
				$parser = new $parserClass($this);
				
				$this->screenings = $parser->getScreenings();
			} else { $this->screenings = null; }
		} catch(\Error $error) {
			Debugger::barDump($error);
			Debugger::log($error, Debugger::ERROR);
		} catch(\Exception $exception) {
			Debugger::barDump($exception);
			Debugger::log($exception, Debugger::EXCEPTION);
		}
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
				for($i=0; $i<count($this->screenings->toArray()); $i++) {
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
