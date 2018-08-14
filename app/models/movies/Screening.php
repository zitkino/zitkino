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

	public function addShowtime($showtime) {
		$this->showtimes[] = $showtime;
	}
}
