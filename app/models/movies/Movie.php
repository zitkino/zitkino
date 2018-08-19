<?php
namespace Zitkino\Movies;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;

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
    
    /** @var Screening[] */
    protected $screenings;
    
    
    public function __construct($name) {
		$this->name = $name;
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
		
		$imdbUrl = "http://www.imdb.com";
		if(isset($this->imdb)) {
			$this->databases["imdb"] = $imdbUrl."/title/".$this->imdb;
		} else { $this->databases["imdb"] = $imdbUrl."/find?s=tt&q=".urlencode($this->name); }
	}
	
	public function addScreening($screening) {
    	$this->screenings[] = $screening;
	}
}
