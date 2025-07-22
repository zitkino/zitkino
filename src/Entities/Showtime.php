<?php

namespace App\Entities;

use App\Repositories\ShowtimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShowtimeRepository::class)]
#[ORM\Table(name: "zk_showtimes", indexes: [new ORM\Index(columns: ["screening"], name: "screening")])]
class Showtime {
	#[ORM\Column(name: "id", type: "integer", nullable: false)]
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: "IDENTITY")]
	private $id;
	
	#[ORM\ManyToOne(targetEntity: Screening::class, inversedBy: "showtimes")]
	#[ORM\JoinColumn(name: "screening", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
	private Screening $screening;
	
	#[ORM\Column(name: "datetime", type: "datetime", nullable: false)]
	private \DateTime $datetime;
	
	public function __construct(Screening $screening, \DateTime $datetime) {
		$this->screening = $screening;
		$this->datetime = $datetime;
		$this->fixDatetime();
	}
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getScreening(): ?Screening {
		return $this->screening;
	}
	
	public function setScreening(?Screening $screening): self {
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
