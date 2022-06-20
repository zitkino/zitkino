<?php
namespace Zitkino\Screenings;

use Dobine\Attributes\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * Showtime
 *
 * @ORM\Table(name="showtimes", indexes={@ORM\Index(name="screening", columns={"screening"})})
 * @ORM\Entity
 */
class Showtime {
	use Id;
	
	/**
	 * @var Screening
	 * @ORM\ManyToOne(targetEntity="Screening", inversedBy="showtimes")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="screening", referencedColumnName="id", nullable=false, onDelete="CASCADE")
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
	
	public function fixDatetime(): void {
		$currentDate = new \DateTime();
		if($currentDate->format("m") == "12" and $this->datetime->format("m") == "01") {
			$year = (int)$this->datetime->format("Y");
			$nextYear = (int)$currentDate->format("Y") + 1;
			
			if($year < $nextYear) {
				$year++;
			}
			
			$this->datetime->setDate($year, (int)$this->datetime->format("m"), (int)$this->datetime->format("d"));
		}
	}
	
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
