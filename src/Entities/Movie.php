<?php

namespace App\Entities;

use App\Repositories\MovieRepository;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Dobine\Properties\Ids\Id;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ORM\Table(name: "zk_movies", uniqueConstraints: [new ORM\UniqueConstraint(name: "name", columns: ["name"])])]
class Movie {
	use Id;
	
	#[ORM\Column(name: "name", type: "string", length: 191, nullable: false)]
	private string $name;
	
	#[ORM\Column(name: "length", type: "integer", nullable: true)]
	private ?int $length = null;
	
	#[ORM\Column(name: "csfd", type: "string", length: 255, nullable: true)]
	private ?string $csfd = null;
	
	#[ORM\Column(name: "imdb", type: "string", length: 255, nullable: true)]
	private ?string $imdb = null;
	
	private array $databases;
	
	#[ORM\OneToMany(mappedBy: "movie", targetEntity: Screening::class, cascade: ["persist", "remove"])]
	private Collection $screenings;
	
	public function __construct(string $name) {
		$this->name = $name;
		$this->setDatabases();
		$this->screenings = new ArrayCollection();
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}
	
	public function getScreenings(): Collection {
		return $this->screenings;
	}
	
	public function addScreening(Screening $screening): self {
		if(!$this->screenings->contains($screening)) {
			$this->screenings[] = $screening;
			$screening->setMovie($this);
		}
		
		return $this;
	}
	
	public function removeScreening(Screening $screening): self {
		if($this->screenings->removeElement($screening)) {
			// set the owning side to null (unless already changed)
			if($screening->getMovie() === $this) {
				$screening->setMovie(null);
			}
		}
		
		return $this;
	}
	
	public function getLength(): ?int {
		return $this->length;
	}
	
	public function setLength(?int $length): self {
		$this->length = $length;
		return $this;
	}
	
	public function getCsfd(): ?string {
		return $this->csfd;
	}
	
	public function setCsfd(?string $csfd): self {
		$this->csfd = $csfd;
		return $this;
	}
	
	public function getImdb(): ?string {
		return $this->imdb;
	}
	
	public function setImdb(?string $imdb): self {
		$this->imdb = $imdb;
		return $this;
	}
	
	public function getDatabases(): ?array {
		$this->setDatabases();
		return $this->databases;
	}
	
	public function setDatabases(): void {
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
