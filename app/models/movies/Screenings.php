<?php
namespace Zitkino\Movies;

use Doctrine\Common\Collections\ArrayCollection;

class Screenings extends ArrayCollection {
	public function getMovies() {
		$movies = [];
		
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$movies[] = $screening->movie;
		}
		
		return new Movies($movies);
	}
	
	public function hasTypes() {
		foreach($this->toArray() as $screening) {
			$type = $screening->type;
			if(isset($type)) { return true; }
		}
		return false;
	}
	
	public function hasLanguages() {
		foreach($this->toArray() as $screening) {
			$dubbing = $screening->dubbing;
			$subtitles = $screening->subtitles;
			if(isset($dubbing) or isset($subtitles)) { return true; }
		}
		return false;
	}
	
	public function hasPrices() {
		foreach($this->toArray() as $screening) {
			$price = $screening->price;
			if(isset($price)) { return true; }
		}
		return false;
	}
}
