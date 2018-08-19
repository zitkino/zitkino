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
	public $screening;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="datetime", type="datetime", nullable=false)
	 */
	public $datetime;
	
	
	public function __construct(Screening $screening) {
		$this->screening = $screening;
	}
}
