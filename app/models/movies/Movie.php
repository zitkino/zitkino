<?php
namespace Zitkino\Movies;

use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\Screenings;

/**
 * Movie
 *
 * @ORM\Table(name="movies", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class Movie {
	use Identifier;
	
	/**
	 * @var string
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
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
	
	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	/**
	 * @return Screenings
	 */
	public function getScreenings(): Screenings {
		return $this->screenings;
	}
	
	/**
	 * @param Screenings $screenings
	 * @return Movie
	 */
	public function setScreenings(Screenings $screenings): Movie {
		$this->screenings = $screenings;
		return $this;
	}
	
	/**
	 * @return int|null
	 */
	public function getLength(): ?int {
		return $this->length;
	}
	
	/**
	 * @param int|null $length
	 * @return Movie
	 */
	public function setLength(?int $length): Movie {
		$this->length = $length;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getCsfd(): ?string {
		return $this->csfd;
	}
	
	/**
	 * @param string|null $csfd
	 * @return Movie
	 */
	public function setCsfd(?string $csfd): Movie {
		$this->csfd = $csfd;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getImdb(): ?string {
		return $this->imdb;
	}
	
	/**
	 * @param string|null $imdb
	 * @return Movie
	 */
	public function setImdb(?string $imdb): Movie {
		$this->imdb = $imdb;
		return $this;
	}
	
	/**
	 * @return array|null
	 */
	public function getDatabases(): ?array {
		$this->setDatabases();
		return $this->databases;
	}
	
	public function setDatabases() {
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
	
	public function addScreening(Screening $screening) {
		$this->screenings[] = $screening;
	}
}
