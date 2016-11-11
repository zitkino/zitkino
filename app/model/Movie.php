<?php
namespace Zitkino;

/**
 * Movie
 */
class Movie {
	private $name, $link;
	private $type, $language, $subtitles;
	private $datetimes;
	private $price;
	
	function getName() {
		return $this->name;
	}
	function getLink() {
		return $this->link;
	}
	function getType() {
		return $this->type;
	}
	function getLanguage() {
		return $this->language;
	}
	function getSubtitles() {
		return $this->subtitles;
	}
	function getDatetimes() {
		return $this->datetimes;
	}
	function getPrice() {
		return $this->price;
	}

	function setName($name) {
		$this->name = $name;
	}
	function setLink($link) {
		$this->link = $link;
	}
	function setType($type) {
		$this->type = $type;
	}
	function setLanguage($language) {
		$this->language = $language;
	}
	function setSubtitles($subtitles) {
		$this->subtitles = $subtitles;
	}
	function setDatetimes($datetimes) {
		$this->datetimes = $datetimes;
	}
	function setPrice($price) {
		$this->price = $price;
	}
	
	public function __construct($name, $datetimes) {
		$this->name = $name;
		$this->datetimes = $datetimes;
	}
}
