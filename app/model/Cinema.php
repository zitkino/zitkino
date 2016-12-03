<?php
namespace Zitkino;

/**
 * Cinema.
 */
class Cinema {
	private $id, $name, $shortName, $type, $address, $city, $gmaps, $url, $programme, $facebook, $twitter, $googleplus;

	public function __construct($id) {
		$ini = parse_ini_file(__DIR__."/../database.ini");
		$connectionParams = array(
			"dbname" => $ini["database"],
			"user" => $ini["user"], "password" => $ini["password"],
			"host" => $ini["server"],
			"driver" => "pdo_mysql", "charset"  => "utf8",
			"driverOptions" => array(1002 => "SET NAMES utf8")
		);
		$config = new \Doctrine\DBAL\Configuration();
		
		$connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		
		if(is_numeric($id)) {
			$statement = $connection->executeQuery("SELECT * FROM cinemas WHERE id = ?", array($id));
			while($row = $statement->fetch()) {
				$this->id = $id;
				$this->name = $row["name"];
				$this->shortName = $row["shortName"];
				$this->type = $row["type"];
				$this->address = $row["address"];
				$this->city = $row["city"];
				$this->gmaps = $row["gmaps"];
				$this->url = $row["url"];
				$this->programme = $row["programme"];
				$this->facebook = $row["facebook"];
				$this->twitter = $row["twitter"];
				$this->googleplus = $row["google+"];
			}
		}
		else {
			$statement = $connection->executeQuery("SELECT * FROM cinemas WHERE shortName = ?", array($id));
			while($row = $statement->fetch()) {
				$this->id = $row["id"];
				$this->name = $row["name"];
				$this->shortName = $id;
				$this->type = $row["type"];
				$this->address = $row["address"];
				$this->city = $row["city"];
				$this->gmaps = $row["gmaps"];
				$this->url = $row["url"];
				$this->programme = $row["programme"];
				$this->facebook = $row["facebook"];
				$this->twitter = $row["twitter"];
				$this->googleplus = $row["google+"];
			}
		}
	}
	
	function getId() {
		return $this->id;
	}
	function getName() {
		return $this->name;
	}
	function getShortName() {
		return $this->shortName;
	}
	function getType() {
		return $this->type;
	}
	function getAddress() {
		return $this->address;
	}
	function getCity() {
		return $this->city;
	}
	function getGmaps() {
		return $this->gmaps;
	}
	function getUrl() {
		return $this->url;
	}
	function getProgramme() {
		return $this->programme;
	}
	function getFacebook() {
		return $this->facebook;
	}
	function getTwitter() {
		return $this->twitter;
	}
	function getGoogleplus() {
		return $this->googleplus;
	}
	
	public function getMovies() {
		$parser = "\Zitkino\parsers\\".ucfirst($this->getShortName())."Parser";
		if(class_exists($parser)) {
			$pa = new $parser();
			return $pa->getMovies();
		}
		else {
			return null;
		}
	}
	
	public function getSoonestMovies() {
		$movies = $this->getMovies();
		$soonest = [];
		
		if(!is_null($movies)) {
			foreach($movies as $movie) {
				$currentDate = new \DateTime();
				
				$nextDate = new \DateTime();
				$nextDate->modify("+1 days");
				
				$datetimes = [];
				foreach($movie->getDatetimes() as $datetime) {
					// checks if movie is played from now to +1 day
					if ($currentDate < $datetime and $datetime < $nextDate) {
						array_push($datetimes, $datetime);
					}
				}
				
				if(!empty($datetimes)) {
					$movie->setDatetimes($datetimes);
					array_push($soonest, $movie);
				}
			}
		}
		
		if(empty($soonest)) {
			if(is_null($movies)) {
				$soonest = null;
			}
			else {
				$soonest = [$movies[0]];
			}
		}
		
		return $soonest;
	}
}
