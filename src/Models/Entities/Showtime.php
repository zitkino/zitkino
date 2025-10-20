<?php

namespace App\Models\Entities;

use App\Models\Repositories\ShowtimeRepository;
use Dobine\Properties\Ids\Id;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShowtimeRepository::class)]
#[ORM\Table(name: "zk_showtimes", indexes: [new ORM\Index(columns: ["screening"], name: "screening")])]
class Showtime {
	use Id;
	
	#[ORM\ManyToOne(targetEntity: Screening::class, inversedBy: "showtimes")]
	#[ORM\JoinColumn(name: "screening", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
	private Screening $screening;
	
	#[ORM\Column(name: "datetime", type: "datetime", nullable: false)]
	private \DateTime $datetime;
	
	public function __construct(?Screening $screening = null, ?\DateTime $datetime = null) {
		if(isset($screening)) {
			$this->screening = $screening;
		}
		
		if(isset($datetime)) {
			$this->datetime = $datetime;
			$this->fixDatetime();
		}
	}
	
	public function getScreening(): Screening {
		return $this->screening;
	}
	
	public function setScreening(Screening $screening): self {
		$this->screening = $screening;
		return $this;
	}
	
	public function getDatetime(): \DateTime {
		return $this->datetime;
	}
	
	public function setDatetime(\DateTime $datetime): self {
		$this->datetime = $datetime;
		return $this;
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
