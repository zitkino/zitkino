<?php
namespace Zitkino\Movies;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * Showtime
 *
 * @ORM\Table(name="showtimes", indexes={@ORM\Index(name="screening", columns={"screening"})})
 * @ORM\Entity
 */
class Showtime {
	use MagicAccessors;
	
	/**
	 * @var int
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;
	
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
}
