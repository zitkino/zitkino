<?php

namespace App\Models\Showtime;

use App\Models\Screening\Screening;
use Dobine\Properties\Ids\Id;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "zk_showtimes")]
#[ORM\Index(name: "screening", columns: ["screening"])]
class Showtime {
	use Id;
	
	#[ORM\ManyToOne(targetEntity: Screening::class, inversedBy: "showtimes")]
	#[ORM\JoinColumn(name: "screening", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
	public Screening $screening {
		get => $this->screening;
		set => $this->screening = $value;
	}
	
	#[ORM\Column(name: "datetime", type: "datetime", nullable: false)]
	public \DateTime $datetime {
		get => $this->datetime;
		set {
			$this->datetime = $value;
			$this->fixDatetime();
		}
	}
	
	public function __construct(?Screening $screening = null, ?\DateTime $datetime = null) {
		if(isset($screening)) {
			$this->screening = $screening;
		}
		
		if(isset($datetime)) {
			$this->datetime = $datetime;
		}
	}
	
	public function fixDatetime(): void {
		$currentDate = new \DateTime();
		if($currentDate->format("m") == "12" && $this->datetime->format("m") == "01") {
			$year = (int)$this->datetime->format("Y");
			$nextYear = (int)$currentDate->format("Y") + 1;
			
			if($year < $nextYear) {
				$year++;
			}
			
			$this->datetime->setDate($year, (int)$this->datetime->format("m"), (int)$this->datetime->format("d"));
		}
	}
	
	public function isActual(): bool {
		if($this->datetime > new \DateTime()) {
			return true;
		} else {
			return false;
		}
	}
}
