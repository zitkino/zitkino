<?php
namespace Zitkino;

/**
 * Movie.
 */
class Movie {
	private $name, $link, $type, $language, $subtitles, $length, $price, $csfd, $imdb;
	/** @var \DateTime[] $datetimes */
	private $datetimes;
	/** @var string[] $databases */
	private $databases;
	
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getLink() {
		return $this->link;
	}
	public function setLink($link) {
		$this->link = $link;
	}
	
	public function getType() {
		return $this->type;
	}
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getLanguage() {
		return $this->language;
	}
	public function setLanguage($language) {
		$this->language = $language;
	}
	
	public function getSubtitles() {
		return $this->subtitles;
	}
	public function setSubtitles($subtitles) {
		$this->subtitles = $subtitles;
	}
	
	public function getDatetimes() {
		return $this->datetimes;
	}
	public function setDatetimes($datetimes) {
		$this->datetimes = $datetimes;
	}
	public function fixDatetimes() {
		foreach($this->datetimes as $datetime) {
			$currentDate = new \DateTime();
			if($currentDate->format("m") == "12" and $datetime->format("m") == "01") {
				$year = (int)$datetime->format("Y");
				$nextYear = (int)$currentDate->format("Y") + 1;
				if($year < $nextYear) {
					$year++;
				}
				$datetime->setDate($year, $datetime->format("m"), $datetime->format("d"));
			}
		}
	}
	
	public function getLength() {
		return $this->length;
	}
	public function setLength($length) {
		$this->length = $length;
	}
	
	public function getPrice() {
		return $this->price;
	}
	public function setPrice($price) {
		$this->price = $price;
	}
	public function fixPrice() {
		if(!isset($this->price) or !is_numeric($this->price)) {
			return null;
		} elseif($this->price == 0) {
			return "zdarma";
		} else {
			return $this->price." Kč";
		}
	}
	
	public function getCsfd() {
		return $this->csfd;
	}
	public function setCsfd($csfd) {
		$this->csfd = $csfd;
	}
	
	public function getImdb() {
		return $this->imdb;
	}
	public function setImdb($imdb) {
		$this->imdb = $imdb;
	}
	
	public function getDatabases() {
		$this->fixDatabases();
		return $this->databases;
	}
	public function setDatabases($databases) {
		$this->databases = $databases;
	}
	
	public function __construct($name, $datetimes) {
		$this->name = $name;
		$this->fixDatabases();
		$this->datetimes = $datetimes;
		$this->fixDatetimes();
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
}
