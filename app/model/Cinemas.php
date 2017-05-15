<?php
namespace Zitkino;
use \Lib\database\Doctrine as DB;

/**
 * Cinemas.
 */
class Cinemas {
	private $classic = [];
	private $summer = [];
	
	function getClassic() {
		return $this->classic;
	}

	function getSummer() {
		return $this->summer;
	}

	public function __construct() {
		$db = new DB(__DIR__."/../database.ini");
		$connection = $db->getConnection();
		
		$statement = $connection->executeQuery("SELECT id FROM cinemas ORDER BY shortName");
		while($row = $statement->fetch()) {
			$cinema = new \Zitkino\Cinema($row["id"]);
			$data = $cinema->getData();
			switch($data["type"]) {
				case "classic":
					array_push($this->classic, $cinema);
					break;
				case "summer":
					array_push($this->summer, $cinema);
					break;
				
				default:
					break;
			}
		}
	}
}
