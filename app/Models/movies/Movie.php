<?php
namespace Zitkino\Movies;

use Dobine\Attributes\Id;
use Doctrine\ORM\Mapping as ORM;
use Zitkino\Screenings\{Screening, Screenings};

/**
 * Movie
 *
 * @ORM\Table(name="movies", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class Movie {
	use Id;
	
	/**
	 * @var string
	 * @ORM\Column(name="name", type="string", length=191, nullable=false)
	 */
	protected $name;
	
	/**
	 * @var int|null
	 * @ORM\Column(name="length", type="integer", nullable=true)
	 */
	protected $length;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="csfd", type="string", length=255, nullable=true)
	 */
	protected $csfd;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="imdb", type="string", length=255, nullable=true)
	 */
	protected $imdb;
	
	/** @var array */
	protected $databases;
	
	/**
	 * @var Screenings
	 * @ORM\OneToMany(targetEntity="\Zitkino\Screenings\Screening", mappedBy="movie", cascade={"persist", "remove"})
	 */
	protected $screenings;
	
	public function __construct(string $name) {
		$this->name = $name;
		$this->setDatabases();
		$this->screenings = new Screenings();
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function getScreenings(): Screenings {
		return $this->screenings;
	}
	
	public function setScreenings(Screenings $screenings): Movie {
		$this->screenings = $screenings;
		return $this;
	}
	
	public function getLength(): ?int {
		return $this->length;
	}
	
	public function setLength(?int $length): Movie {
		$this->length = $length;
		return $this;
	}
	
	public function getCsfd(): ?string {
		return $this->csfd;
	}
	
	public function setCsfd(?string $csfd): Movie {
		$this->csfd = $csfd;
		return $this;
	}
	
	public function getImdb(): ?string {
		return $this->imdb;
	}
	
	public function setImdb(?string $imdb): Movie {
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
	
	public function addScreening(Screening $screening): void {
		$this->screenings[] = $screening;
	}
}
