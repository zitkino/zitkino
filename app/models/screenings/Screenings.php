<?php
namespace Zitkino\Screenings;

use Doctrine\Common\Collections\ArrayCollection;
use Zitkino\Movies\Movies;

class Screenings extends ArrayCollection {
	public function __construct(array $screenings = []) {
		parent::__construct($screenings);
	}
	
	public function getMovies(): Movies {
		$movies = [];
		
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$movies[] = $screening->getMovie();
		}
		
		return new Movies($movies);
	}
	
	public function hasTypes(): bool {
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$type = $screening->getType();
			if(isset($type) and $type->getCode() !== "2D") {
				return true;
			}
		}
		return false;
	}
	
	public function hasPlaces(): bool {
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$place = $screening->getPlace();
			if(isset($place)) {
				return true;
			}
		}
		return false;
	}
	
	public function hasLanguages(): bool {
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$dubbing = $screening->getDubbing();
			$subtitles = $screening->getSubtitles();
			if(isset($dubbing) or isset($subtitles)) {
				return true;
			}
		}
		return false;
	}
	
	public function hasPrices(): bool {
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$price = $screening->getPrice();
			if(isset($price)) {
				return true;
			}
		}
		return false;
	}
}
