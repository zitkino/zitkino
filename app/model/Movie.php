<?php
namespace Zitkino;

/**
 * Movie.
 */
class Movie {
	private $name, $link, $csfd, $imdb, $databases, $type, $language, $subtitles, $datetimes, $price;
	
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
	
	public function getPrice() {
		return $this->price;
	}
	public function setPrice($price) {
		$this->price = $price;
	}
	
	public function __construct($name, $datetimes) {
		$this->name = $name;
		$this->fixDatabases();
		$this->datetimes = $datetimes;
	}
	
	public function fixDatabases() {
		$csfdUrl = "http://www.csfd.cz";
		if(isset($this->csfd)) {
			$this->databases["csfd"] = $csfdUrl."/film/".$this->csfd;
		} else { $this->databases["csfd"] = $csfdUrl."/hledat/?q=".urlencode($this->name); }
		
		$imdbUrl = "http://www.imdb.com";
		if(isset($this->imdb)) {
			$this->databases["imdb"] = $imdbUrl."/title/".$this->imdb;
		} else { $this->databases["imdb"] = $imdbUrl."/find?s=tt&q=".urlencode($this->name); }
	}
}
