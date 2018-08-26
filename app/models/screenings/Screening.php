<?php
namespace Zitkino\Screenings;

use Kdyby\Doctrine\Entities\MagicAccessors;
use Doctrine\ORM\Mapping as ORM;
use Zitkino\Cinemas\Cinema;
use Zitkino\Language;
use Zitkino\Movies\Movie;

/**
 * Screening
 *
 * @ORM\Table(name="screenings", indexes={@ORM\Index(name="language", columns={"dubbing"}), @ORM\Index(name="subtitles", columns={"subtitles"}), @ORM\Index(name="movie", columns={"movie"}), @ORM\Index(name="cinema", columns={"cinema"}), @ORM\Index(name="type", columns={"type"})})
 * @ORM\Entity
 */
class Screening {
	use MagicAccessors;
	
	/**
	 * @var int
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;
	
	/**
	 * @var int|null
	 * @ORM\Column(name="price", type="integer", nullable=true)
	 */
	public $price;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="link", type="string", length=1000, nullable=true)
	 */
	public $link;
	
	/**
	 * @var Movie
	 * @ORM\ManyToOne(targetEntity="Movie")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="movie", referencedColumnName="id")
	 * })
	 */
	public $movie;
	
	/**
	 * @var Cinema
	 * @ORM\ManyToOne(targetEntity="Cinema")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="cinema", referencedColumnName="id")
	 * })
	 */
	public $cinema;
	
	/**
	 * @var Language
	 * @ORM\ManyToOne(targetEntity="Language")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="dubbing", referencedColumnName="id")
	 * })
	 */
	public $dubbing;
	
	/**
	 * @var Language
	 * @ORM\ManyToOne(targetEntity="Language")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="subtitles", referencedColumnName="id")
	 * })
	 */
	public $subtitles;
	
	/**
	 * @var ScreeningType
	 * @ORM\ManyToOne(targetEntity="ScreeningType")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type", referencedColumnName="id")
	 * })
	 */
	public $type;
	
	/** @var Showtime[] */
	protected $showtimes;
	
	
	/**
	 * @param Language|string $dubbing
	 * @param Language|string $subtitles
	 * @return Screening
	 */
	public function setLanguages($dubbing, $subtitles): Screening {
		$this->dubbing = $dubbing;
		$this->subtitles = $subtitles;
		return $this;
	}
	
	
	public function __construct(Movie $movie, Cinema $cinema) {
		$this->movie = $movie;
		$this->cinema = $cinema;
	}


	public function addShowtime($showtime) {
		$this->showtimes[] = $showtime;
	}

	/**
	 * @return Showtime[]
	 */
	public function getShowtimes(): array {
		if(!isset($this->showtimes)) {
			return [];
		}
		
		return $this->showtimes;
	}

	/**
	 * @param \DateTime[] $datetimes
	 * @param bool $actual
	 */
	public function setShowtimes($datetimes, $actual = true) {
		foreach($datetimes as $datetime) {
			$showtime = new Showtime($this);
			$showtime->datetime = $datetime;
			
			if($actual === true) {
				if(isset($showtime->datetime) and $showtime->isActual()) {
					$this->addShowtime($showtime);
				}
			} elseif ($actual === false) {
				$this->addShowtime($showtime);
			}
		}
	}
	
	public function fixPrice() {
		if(!isset($this->price) or !is_numeric($this->price)) {
			return null;
		} elseif($this->price == 0) {
			return "zdarma";
		} else {
			return $this->price." Kč";
		}
	}
}
