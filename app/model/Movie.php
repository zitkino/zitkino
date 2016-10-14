<?php
namespace Zitkino;

/**
 * Movie
 */
class Movie {
	private $name, $datetime, $link;
	
	function getName() {
		return $this->name;
	}
	function getDatetime() {
		return $this->datetime;
	}
	function getLink() {
		return $this->link;
	}

	function setName($name) {
		$this->name = $name;
	}
	function setDatetime($datetime) {
		$this->datetime = $datetime;
	}
	function setLink($link) {
		$this->link = $link;
	}

	public function __construct($name, $datetime) {
		$this->name = $name;
		$this->datetime = $datetime;
	}
}
