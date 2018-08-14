<?php
namespace Zitkino\Movies;

use Kdyby\Doctrine\Entities\MagicAccessors;
use Doctrine\ORM\Mapping as ORM;
use Zitkino\Cinemas\Cinema;
use Zitkino\Language;

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
	 * @var \DateTime|null
	 * @ORM\Column(name="date", type="date", nullable=true)
	 */
	protected $date;
	
	/**
	 * @var \DateTime|null
	 * @ORM\Column(name="time", type="time", nullable=true)
	 */
	protected $time;
	
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
	 * @var Movie
	 * @ORM\ManyToOne(targetEntity="Movie")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="movie", referencedColumnName="id")
	 * })
	 */
	protected $movie;
	
	/**
	 * @var Cinema
	 * @ORM\ManyToOne(targetEntity="Cinema")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="cinema", referencedColumnName="id")
	 * })
	 */
	protected $cinema;
	
	/**
	 * @var Language
	 * @ORM\ManyToOne(targetEntity="Language")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="dubbing", referencedColumnName="id")
	 * })
	 */
	protected $dubbing;
	
	/**
	 * @var Language
	 * @ORM\ManyToOne(targetEntity="Language")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="subtitles", referencedColumnName="id")
	 * })
	 */
	protected $subtitles;
	
	/**
	 * @var ScreeningType
	 * @ORM\ManyToOne(targetEntity="ScreeningType")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type", referencedColumnName="id")
	 * })
	 */
	protected $type;
	
	/** @var Showtime[] */
	protected $showtimes;
	
	
	/**
	 * @return \DateTime|null
	 */
	public function getDate(): \DateTime {
		return $this->date;
	}
	
	/**
	 * @param \DateTime|null $date
	 * @return Screening
	 */
	public function setDate(\DateTime $date): Screening {
		$this->date = $date;
		return $this;
	}
	
	/**
	 * @return \DateTime|null
	 */
	public function getTime(): \DateTime {
		return $this->time;
	}
	
	/**
	 * @param \DateTime|null $time
	 * @return Screening
	 */
	public function setTime(\DateTime $time): Screening {
		$this->time = $time;
		return $this;
	}
	
	/**
	 * @return int|null
	 */
	public function getPrice(): int {
		return $this->price;
	}
	
	/**
	 * @param int|null $price
	 * @return Screening
	 */
	public function setPrice(int $price): Screening {
		$this->price = $price;
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getLink(): string {
		return $this->link;
	}
	
	/**
	 * @param null|string $link
	 * @return Screening
	 */
	public function setLink(string $link): Screening {
		$this->link = $link;
		return $this;
	}
	
	/**
	 * @return Movie
	 */
	public function getMovie(): Movie {
		return $this->movie;
	}
	
	/**
	 * @param Movie $movie
	 * @return Screening
	 */
	public function setMovie(Movie $movie): Screening {
		$this->movie = $movie;
		return $this;
	}
	
	/**
	 * @return Cinema
	 */
	public function getCinema(): Cinema {
		return $this->cinema;
	}
	
	/**
	 * @param Cinema $cinema
	 * @return Screening
	 */
	public function setCinema(Cinema $cinema): Screening {
		$this->cinema = $cinema;
		return $this;
	}
	
	/**
	 * @return Language
	 */
	public function getDubbing(): Language {
		return $this->dubbing;
	}
	
	/**
	 * @param Language $dubbing
	 * @return Screening
	 */
	public function setDubbing(Language $dubbing): Screening {
		$this->dubbing = $dubbing;
		return $this;
	}
	
	/**
	 * @return Language
	 */
	public function getSubtitles(): Language {
		return $this->subtitles;
	}
	
	/**
	 * @param Language $subtitles
	 * @return Screening
	 */
	public function setSubtitles(Language $subtitles): Screening {
		$this->subtitles = $subtitles;
		return $this;
	}
	
	/**
	 * @return ScreeningType
	 */
	public function getType(): ScreeningType {
		return $this->type;
	}
	
	/**
	 * @param ScreeningType $type
	 * @return Screening
	 */
	public function setType(ScreeningType $type): Screening {
		$this->type = $type;
		return $this;
	}
	
	/**
	 * @return Showtime[]
	 */
	public function getShowtimes(): array {
		return $this->showtimes;
	}
	
	/**
	 * @param Showtime[] $showtimes
	 * @return Screening
	 */
	public function setShowtimes(array $showtimes): Screening {
		$this->showtimes = $showtimes;
		return $this;
	}
	
	
	public function addShowtime($showtime) {
		$this->showtimes[] = $showtime;
	}
}
