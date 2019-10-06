<?php
namespace Zitkino\Screenings;

use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Zitkino\Cinemas\Cinema;
use Zitkino\Language;
use Zitkino\Movies\Movie;
use Zitkino\Place;

/**
 * Screening
 *
 * @ORM\Table(name="screenings", indexes={@ORM\Index(name="movie", columns={"movie"}), @ORM\Index(name="cinema", columns={"cinema"}), @ORM\Index(name="type", columns={"type"}), @ORM\Index(name="dubbing", columns={"dubbing"}), @ORM\Index(name="subtitles", columns={"subtitles"})})
 * @ORM\Entity
 */
class Screening {
	use Identifier, MagicAccessors;
	
	/**
	 * @var Movie
	 * @ORM\ManyToOne(targetEntity="\Zitkino\Movies\Movie")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="movie", referencedColumnName="id", nullable=false)
	 * })
	 */
	protected $movie;
	
	/**
	 * @var Cinema
	 * @ORM\ManyToOne(targetEntity="\Zitkino\Cinemas\Cinema")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="cinema", referencedColumnName="id", nullable=false)
	 * })
	 */
	protected $cinema;
	
	/**
	 * @var ScreeningType|null
	 * @ORM\ManyToOne(targetEntity="ScreeningType")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=true)
	 * })
	 */
	protected $type;
	
	/**
	 * @var Place|null
	 * @ORM\ManyToOne(targetEntity="\Zitkino\Place")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="place", referencedColumnName="id", nullable=true)
	 * })
	 */
	protected $place;
	
	/**
	 * @var Language|string|null
	 * @ORM\ManyToOne(targetEntity="\Zitkino\Language")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="dubbing", referencedColumnName="id", nullable=true)
	 * })
	 */
	protected $dubbing;
	
	/**
	 * @var Language|string|null
	 * @ORM\ManyToOne(targetEntity="\Zitkino\Language")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="subtitles", referencedColumnName="id", nullable=true)
	 * })
	 */
	protected $subtitles;
	
	/**
	 * @var int|null
	 * @ORM\Column(name="price", type="integer", nullable=true)
	 */
	protected $price;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="link", type="string", length=1000, nullable=true)
	 */
	protected $link;
	
	/** @var Showtime[] */
	protected $showtimes;
	
	public function __construct(Movie $movie, Cinema $cinema) {
		$this->movie = $movie;
		$this->cinema = $cinema;
		$this->showtimes = [];
	}
	
	/**
	 * @return int|null
	 */
	public function getPrice(): ?int {
		return $this->price;
	}
	
	/**
	 * @param int|string|null $price
	 * @return Screening
	 */
	public function setPrice($price): Screening {
		if(isset($price) and !empty($price)) {
			$this->price = intval($price);
		} else {
			$this->price = null;
		}
		
		return $this;
	}
	
	public function fixPrice() {
		if(!isset($this->price) or !is_numeric($this->price)) {
			return null;
		} else {
			if($this->price == 0) {
				return "zdarma";
			} else {
				return $this->price." Kč";
			}
		}
	}
	
	/**
	 * @return null|string
	 */
	public function getLink(): ?string {
		return $this->link;
	}
	
	/**
	 * @param null|string $link
	 * @return Screening
	 */
	public function setLink(?string $link): Screening {
		$this->link = $link;
		return $this;
	}
	
	/**
	 * @return Language|string|null
	 */
	public function getDubbing() {
		return $this->dubbing;
	}
	
	/**
	 * @param Language|string|null $dubbing
	 * @return Screening
	 */
	public function setDubbing($dubbing): Screening {
		$this->dubbing = $dubbing;
		return $this;
	}
	
	/**
	 * @return Language|string|null
	 */
	public function getSubtitles() {
		return $this->subtitles;
	}
	
	/**
	 * @param Language|string|null $subtitles
	 * @return Screening
	 */
	public function setSubtitles($subtitles): Screening {
		$this->subtitles = $subtitles;
		return $this;
	}
	
	/**
	 * @return ScreeningType|null
	 */
	public function getType(): ?ScreeningType {
		return $this->type;
	}
	
	/**
	 * @param ScreeningType|null $type
	 * @return Screening
	 */
	public function setType(?ScreeningType $type): Screening {
		$this->type = $type;
		return $this;
	}
	
	/**
	 * @return Place|null
	 */
	public function getPlace(): ?Place {
		return $this->place;
	}
	
	/**
	 * @param Place|null $place
	 * @return Screening
	 */
	public function setPlace(?Place $place): Screening {
		$this->place = $place;
		return $this;
	}
	
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
	
	/**
	 * @return Showtime[]
	 */
	public function getShowtimes(): array {
		return $this->showtimes;
	}
	
	/**
	 * @param \DateTime[] $datetimes
	 * @param bool $actual
	 */
	public function setShowtimes($datetimes, $actual = true) {
		foreach($datetimes as $datetime) {
			$showtime = new Showtime($this, $datetime);
			
			if($actual === true) {
				if($showtime->isActual()) {
					$this->addShowtime($showtime);
				}
			} else {
				if($actual === false) {
					$this->addShowtime($showtime);
				}
			}
		}
	}
	
	public function addShowtime($showtime) {
		$this->showtimes[] = $showtime;
	}
	
	/**
	 * @return Movie
	 */
	public function getMovie(): Movie {
		return $this->movie;
	}
}
