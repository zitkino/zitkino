<?php
namespace Zitkino\Movies;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * Showtime
 *
 * @ORM\Table(name="showtimes", indexes={@ORM\Index(name="screening", columns={"screening"})})
 * @ORM\Entity
 */
class Showtime {
	use MagicAccessors, Identifier;
	
	/**
	 * @var Screening
	 * @ORM\ManyToOne(targetEntity="Screening")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="screening", referencedColumnName="id")
	 * })
	 */
	protected $screening;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="datetime", type="datetime", nullable=false)
	 */
	protected $datetime;
	
	
	/**
	 * @return Screening
	 */
	public function getScreening(): Screening {
		return $this->screening;
	}
	
	/**
	 * @param Screening $screening
	 * @return Showtime
	 */
	public function setScreening(Screening $screening): Showtime {
		$this->screening = $screening;
		return $this;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getDatetime(): \DateTime {
		return $this->datetime;
	}
	
	/**
	 * @param \DateTime $datetime
	 * @return Showtime
	 */
	public function setDatetime(\DateTime $datetime): Showtime {
		$this->datetime = $datetime;
		return $this;
	}
}
