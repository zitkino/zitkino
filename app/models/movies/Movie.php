<?php
namespace Zitkino\Movies;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Zitkino\Screenings\Screenings;

/**
 * Movie
 *
 * @ORM\Table(name="movies")
 * @ORM\Entity
 */
class Movie {
	use Identifier, MagicAccessors;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="name", type="string", length=255, nullable=true)
	 */
	public $name;
	
	/**
	 * @var int|null
	 * @ORM\Column(name="length", type="integer", nullable=true)
	 */
	public $length;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="csfd", type="string", length=255, nullable=true)
	 */
	public $csfd;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="imdb", type="string", length=255, nullable=true)
	 */
	public $imdb;
	
	/** @var array */
	public $databases;
	
	/** @var array */
	protected $datetimes;
	
	/** @var Screenings */
	protected $screenings;
	
	
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
	
	
	public function __construct(string $name) {
		$this->name = $name;
		$this->fixDatabases();
	}

	/**
	 * @return array
	 */
	public function getDatabases(): array {
		return $this->databases;
	}
	
	public function setDatabases(array $databases) {
		$this->databases = $databases;
		$this->fixDatabases();
	}
	
	public function fixDatabases() {
		$csfdUrl = "https://www.csfd.cz";
		if(isset($this->csfd)) {
			$this->databases["csfd"] = $csfdUrl."/film/".$this->csfd;
		} else { $this->databases["csfd"] = $csfdUrl."/hledat/?q=".urlencode($this->name); }
		
		$imdbUrl = "https://www.imdb.com";
		if(isset($this->imdb)) {
			$this->databases["imdb"] = $imdbUrl."/title/".$this->imdb;
		} else { $this->databases["imdb"] = $imdbUrl."/find?s=tt&q=".urlencode($this->name); }
	}
	
	public function addScreening($screening) {
		$this->screenings[] = $screening;
	}
}
