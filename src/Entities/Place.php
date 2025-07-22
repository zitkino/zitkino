<?php

namespace App\Entities;

use App\Repositories\PlaceRepository;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
#[ORM\Table(name: "zk_places")]
class Place {
	#[ORM\Column(name: "id", type: "integer", nullable: false)]
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: "IDENTITY")]
	private $id;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
	private string $name;
	
	#[ORM\Column(name: "link", type: "string", length: 255, nullable: true)]
	private ?string $link= null;
	
	#[ORM\ManyToOne(targetEntity: Cinema::class, inversedBy: "places")]
	#[ORM\JoinColumn(name: "cinema", referencedColumnName: "id", nullable: false)]
	private Cinema $cinema;
	
	#[ORM\OneToMany(mappedBy: "place", targetEntity: Screening::class)]
	private ArrayCollection $screenings;
	
	public function __construct(string $name) {
		$this->name = $name;
		$this->screenings = new ArrayCollection();
	}
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}
	
	public function getLink(): ?string {
		return $this->link;
	}
	
	public function setLink(?string $link): self {
		$this->link = $link;
		return $this;
	}
	
	public function getCinema(): Cinema {
		return $this->cinema;
	}
	
	public function setCinema(Cinema $cinema): self {
		$this->cinema = $cinema;
		return $this;
	}
	
	public function getScreenings(): Collection {
		return $this->screenings;
	}
	
	public function addScreening(Screening $screening): self {
		if(!$this->screenings->contains($screening)) {
			$this->screenings[] = $screening;
			$screening->setPlace($this);
		}
		
		return $this;
	}
	
	public function removeScreening(Screening $screening): self {
		if($this->screenings->removeElement($screening)) {
			// set the owning side to null (unless already changed)
			if($screening->getPlace() === $this) {
				$screening->setPlace(null);
			}
		}
		
		return $this;
	}
}
