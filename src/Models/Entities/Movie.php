<?php

namespace App\Models\Entities;

use Dobine\Properties\Ids\Id;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "zk_movies", uniqueConstraints: [new ORM\UniqueConstraint(name: "name", columns: ["name"])])]
class Movie {
	use Id;
	
	#[ORM\Column(name: "name", type: "string", length: 191, nullable: false)]
	public string $name {
		get => $this->name;
		set => $this->name = $value;
	}
	
	#[ORM\Column(name: "length", type: "integer", nullable: true)]
	public ?int $length = null {
		get => $this->length;
		set => $this->length = $value;
	}
	
	#[ORM\Column(name: "csfd", type: "string", length: 255, nullable: true)]
	public ?string $csfd = null {
		get => $this->csfd;
		set => $this->csfd = $value;
	}
	
	#[ORM\Column(name: "imdb", type: "string", length: 255, nullable: true)]
	public ?string $imdb = null {
		get => $this->imdb;
		set => $this->imdb = $value;
	}
	
	private array $databases;
	
	#[ORM\OneToMany(mappedBy: "movie", targetEntity: Screening::class, cascade: ["persist", "remove"])]
	public Collection $screenings {
		get => $this->screenings;
	}
	
	public function __construct(string $name = '') {
		if(!empty($name)) {
			$this->name = $name;
			$this->initDatabases();
		}
		
		$this->screenings = new ArrayCollection();
	}
	
	public function __toString(): string {
		return $this->name;
	}
	
	public function addScreening(Screening $screening): self {
		if(!$this->screenings->contains($screening)) {
			$this->screenings[] = $screening;
			$screening->movie = $this;
		}
		
		return $this;
	}
	
	public function removeScreening(Screening $screening): self {
		if($this->screenings->removeElement($screening)) {
			// set the owning side to null (unless already changed)
			if($screening->movie === $this) {
				$screening->movie = null;
			}
		}
		
		return $this;
	}
	
	public function getDatabases(): ?array {
		$this->initDatabases();
		return $this->databases;
	}
	
	public function initDatabases(): void {
		$csfdUrl = "https://www.csfd.cz";
		if(isset($this->csfd)) {
			$this->databases["csfd"] = $csfdUrl."/film/".$this->csfd;
		} else {
			$this->databases["csfd"] = $csfdUrl."/hledat/?q=".urlencode($this->name);
		}
		
		$imdbUrl = "https://www.imdb.com";
		if(isset($this->imdb)) {
			$this->databases["imdb"] = $imdbUrl."/title/".$this->imdb;
		} else {
			$this->databases["imdb"] = $imdbUrl."/find?s=tt&q=".urlencode($this->name);
		}
	}
}
