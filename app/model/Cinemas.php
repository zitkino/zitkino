<?php
namespace Zitkino;

/**
 * Description of Cinemas
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
		for($id=1; $id<14; $id++) {
			$cinema = new \Zitkino\Cinema($id);
			switch($cinema->getType()) {
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
