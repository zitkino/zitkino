<?php
namespace Zitkino\Screenings;

use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * Showtime
 *
 * @ORM\Table(name="showtimes", indexes={@ORM\Index(name="screening", columns={"screening"})})
 * @ORM\Entity
 */
class Showtime {
	use Identifier;
	
	/**
	 * @var Screening
	 * @ORM\ManyToOne(targetEntity="Screening")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="screening", referencedColumnName="id", nullable=false)
	 * })
	 */
	protected $screening;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="datetime", type="datetime", nullable=false)
	 */
	protected $datetime;
	
	
	public function __construct(Screening $screening, \DateTime $datetime) {
		$this->screening = $screening;
		$this->datetime = $datetime;
		$this->fixDatetime();
	}
	
	
	public function fixDatetime() {
		$currentDate = new \DateTime();
		if($currentDate->format("m") == "12" and $this->datetime->format("m") == "01") {
			$year = (int)$this->datetime->format("Y");
			$nextYear = (int)$currentDate->format("Y") + 1;
			
			if($year < $nextYear) {
				$year++;
			}
			
			$this->datetime->setDate($year, $this->datetime->format("m"), $this->datetime->format("d"));
		}
	}
	
	/**
	 * @return \DateTime
	 */
	public function getDatetime(): \DateTime {
		return $this->datetime;
	}
	
	public function isActual(): bool {
		if($this->datetime > new \DateTime()) {
			return true;
		} else {
			return false;
		}
	}
}
