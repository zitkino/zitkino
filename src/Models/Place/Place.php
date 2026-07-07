<?php

namespace App\Models\Place;

use App\Models\Cinema\Cinema;
use App\Models\Screening\Screening;
use Dobine\Properties\Ids\Id;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
#[ORM\Table(name: "zk_places")]
class Place {
	use Id;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
	public string $name {
		get => $this->name;
		set => $this->name = $value;
	}
	
	#[ORM\Column(name: "link", type: "string", length: 255, nullable: true)]
	public ?string $link = null {
		get => $this->link;
		set => $this->link = $value;
	}
	
	#[ORM\ManyToOne(targetEntity: Cinema::class, inversedBy: "places")]
	#[ORM\JoinColumn(name: "cinema", referencedColumnName: "id", nullable: false)]
	public Cinema $cinema {
		get => $this->cinema;
		set => $this->cinema = $value;
	}
	
	#[ORM\OneToMany(mappedBy: "place", targetEntity: Screening::class)]
	public Collection $screenings {
		get => $this->screenings;
	}
	
	public function __construct(string $name = '') {
		if(!empty($name)) {
			$this->name = $name;
		}
		$this->screenings = new ArrayCollection();
	}
	
	public function __toString(): string {
		return $this->name;
	}
	
	public function addScreening(Screening $screening): self {
		if(!$this->screenings->contains($screening)) {
			$this->screenings[] = $screening;
			$screening->place = $this;
		}
		
		return $this;
	}
}
