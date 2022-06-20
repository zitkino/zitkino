<?php
namespace Zitkino\Screenings;

use Dobine\Attributes\Id;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use Zitkino\Cinemas\Cinema;
use Zitkino\Movies\Movie;
use Zitkino\Place;

/**
 * Screening
 *
 * @ORM\Table(name="screenings", indexes={@ORM\Index(name="movie", columns={"movie"}), @ORM\Index(name="cinema", columns={"cinema"}), @ORM\Index(name="type", columns={"type"}), @ORM\Index(name="dubbing", columns={"dubbing"}), @ORM\Index(name="subtitles", columns={"subtitles"})})
 * @ORM\Entity
 */
class Screening {
	use Id;
	
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
	 * @var string|null
	 * @ORM\Column(name="dubbing", type="string", length=255, nullable=true)
	 */
	protected $dubbing;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="subtitles", type="string", length=255, nullable=true)
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
	
	/**
	 * @var Collection
	 * @ORM\OneToMany(targetEntity="\Zitkino\Screenings\Showtime", mappedBy="screening", cascade={"persist", "remove"})
	 */
	protected $showtimes;
	
	public function __construct(Movie $movie, Cinema $cinema) {
		$this->movie = $movie;
		$this->cinema = $cinema;
		$this->showtimes = new ArrayCollection();
	}
	
	public function __toString() {
		return $this->getMovie()->getId()."-".$this->getCinema()."-".$this->getType()."-".$this->getDubbing()."-".$this->getSubtitles();
	}
	
	public function getMovie(): Movie {
		return $this->movie;
	}
	
	public function getCinema(): Cinema {
		return $this->cinema;
	}
	
	public function getPrice(): ?int {
		return $this->price;
	}
	
	public function setPrice(?int $price): Screening {
		if(isset($price) and !empty($price)) {
			$this->price = intval($price);
		} else {
			$this->price = null;
		}
		
		return $this;
	}
	
	public function fixPrice(): ?string {
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
	
	public function getLink(): ?string {
		return $this->link;
	}
	
	public function setLink(?string $link): Screening {
		$this->link = $link;
		return $this;
	}
	
	public function getDubbing(): ?string {
		return $this->dubbing;
	}
	
	public function setDubbing(?string $dubbing): Screening {
		$this->dubbing = $dubbing;
		return $this;
	}
	
	public function getSubtitles(): ?string {
		return $this->subtitles;
	}
	
	public function setSubtitles(?string $subtitles): Screening {
		$this->subtitles = $subtitles;
		return $this;
	}
	
	public function getType(): ?ScreeningType {
		return $this->type;
	}
	
	public function setType(?ScreeningType $type): Screening {
		$this->type = $type;
		return $this;
	}
	
	public function getPlace(): ?Place {
		return $this->place;
	}
	
	public function setPlace(?Place $place): Screening {
		$this->place = $place;
		return $this;
	}
	
	public function setLanguages(?string $dubbing, ?string $subtitles): Screening {
		$this->dubbing = $dubbing;
		$this->subtitles = $subtitles;
		return $this;
	}
	
	public function getShowtimes(): Collection {
		return $this->showtimes;
	}
	
	public function setShowtimes(array $datetimes, bool $actual = true): void {
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
	
	public function addShowtime(Showtime $showtime): void {
		$this->showtimes[] = $showtime;
	}
}
