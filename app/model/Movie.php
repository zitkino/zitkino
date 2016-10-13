<?php
namespace Zitkino;

/**
 * Movie
 */
class Movie {
	private $name, $datetime;
	
	function getName() {
		return $this->name;
	}
	function getDatetime() {
		return $this->datetime;
	}

	function setName($name) {
		$this->name = $name;
	}
	function setDatetime($datetime) {
		$this->datetime = $datetime;
	}

	public function __construct($name, $datetime) {
		$this->name = $name;
		$this->datetime = $datetime;
	}
}
