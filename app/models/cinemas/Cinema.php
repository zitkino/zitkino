<?php
namespace Zitkino\Cinemas;

use Dobine\Entities\DobineEntity;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Zitkino\Movies\Movie;
use Zitkino\Movies\Screening;
use Zitkino\Movies\Screenings;
use Zitkino\Parsers\Parser;

/**
 * Cinema
 *
 * @ORM\Table(name="cinemas", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})}, indexes={@ORM\Index(name="type", columns={"type"})})
 * @ORM\Entity
 */
class Cinema extends DobineEntity {
	use MagicAccessors;
	
	/**
	 * @var string
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	protected $name;
	
	/**
	 * @var string
	 * @ORM\Column(name="short_name", type="string", length=20, nullable=false)
	 */
	protected $shortName;
	
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
	public $screenings;
	
	
	public function getScreenings() {
		return $this->screenings;
	}
	
	public function setScreenings() {
		try {
			$parserClass = "\Zitkino\Parsers\\".ucfirst($this->shortName);
			if(class_exists($parserClass)) {
				/** @var Parser $parser */
				$parser = new $parserClass($this);
				
				
				$this->screenings = $parser->getScreenings();
				\Tracy\Debugger::barDump([$parser, $this->screenings]);
//				\Tracy\Debugger::barDump($films);
//				if(isset($films)) {
//					foreach($films as $film) {
////						\Tracy\Debugger::barDump($film);
////						if($this->checkActualMovie($film)) {
//							$this->movies[] = $film;
////						}
//					}
//				}
			} else { $this->movies = null; }
		} catch(\Error $error) {
			\Tracy\Debugger::barDump($error);
			\Tracy\Debugger::log($error, \Tracy\Debugger::ERROR);
		} catch(\Exception $exception) {
			\Tracy\Debugger::barDump($exception);
			\Tracy\Debugger::log($exception, \Tracy\Debugger::EXCEPTION);
		}
	}
	
	public function hasMovies() {
		if(isset($this->movies) and !empty($this->movies)) { return true; }
		else { return false; }
	}
	
	public function getSoonestScreenings() {
		return $this->screenings;
		
		$soonest = [];
		if(isset($this->movies)) {
			$currentDate = new \DateTime();
			
			foreach($this->movies as $movie) {
				$nextDate = new \DateTime();
				$nextDate->modify("+1 days");
				
				$datetimes = [];
				foreach($movie->datetimes as $datetime) {
					// checks if movie is played from now to +1 day
					if($currentDate < $datetime and $datetime < $nextDate) {
						$datetimes[] = $datetime;
					}
				}
				
				if(!empty($datetimes)) {
					$movie->datetimes = $datetimes;
					$soonest[] = $movie;
				}
			}
			
			if(count($soonest) < 5) {
				$soonest = [];
				for($i=0; $i<count($this->movies); $i++) {
					if(isset($this->movies[$i])) {
						foreach($this->movies[$i]->getDatetimes() as $datetime) {
							if($currentDate < $datetime) {
								$soonest[] = $this->movies[$i];
							}
						}
					}
					
					if(count($soonest) == 5) {
						break;
					}
				}
			}
		}
		
		if(empty($soonest)) {
			if(is_null($this->movies) or empty($this->movies)) { $soonest = null; }
			else {
				if($this->checkActualMovie($this->movies[0])) {
					$soonest = [$this->movies[0]];
				} else {
					$soonest = null;
				}
			}
		}
		
		return $soonest;
	}
	
	public function checkActualMovie(Movie $movie) {
		$datetime = $movie->getDatetimes()[0];
		if($datetime > new \DateTime()) {
			return true;
		} else {
			return false;
		}
	}
}
