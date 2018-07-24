<?php
namespace Zitkino\Movies;

/**
 * Movies.
 */
class Movies {
	/** @var Movie[] */
	private $movies;
	
	public function __construct($movies) {
		$this->movies = $movies;
	}
	
	public function hasTypes() {
		foreach($this->movies as $movie) {
			$type = $movie->getType();
			if(isset($type)) { return true; }
		}
		return false;
	}
	
	public function hasLanguages() {
		foreach($this->movies as $movie) {
			$dubbing = $movie->getDubbing();
			$subtitles = $movie->getSubtitles();
			if(isset($dubbing) or isset($subtitles)) { return true; }
		}
		return false;
	}
	
	public function hasLengths() {
		foreach($this->movies as $movie) {
			$length = $movie->getLength();
			if(isset($length)) { return true; }
		}
		return false;
	}
	
	public function hasPrices() {
		foreach($this->movies as $movie) {
			$price = $movie->getPrice();
			if(isset($price)) { return true; }
		}
		return false;
	}
}
