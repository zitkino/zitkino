<?php
namespace App\Models\Screening;

use App\Models\Movie\Movies;
use Doctrine\Common\Collections\ArrayCollection;

class Screenings extends ArrayCollection {
	public function __construct(array $screenings = []) {
		parent::__construct($screenings);
	}
	
	public function getMovies(): Movies {
		$movies = [];
		
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$movies[] = $screening->movie;
		}
		
		return new Movies($movies);
	}
	
	public function hasFormats(): bool {
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$format = $screening->format;
			if(isset($format) and $format->getIdent() !== "2D") {
				return true;
			}
		}
		return false;
	}
	
	public function hasTypes(): bool {
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$type = $screening->type;
			if(isset($type)) {
				return true;
			}
		}
		return false;
	}
	
	public function hasPlaces(): bool {
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$place = $screening->place;
			if(isset($place)) {
				return true;
			}
		}
		return false;
	}
	
	public function hasLanguages(): bool {
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$dubbing = $screening->dubbing;
			$subtitles = $screening->subtitles;
			if(isset($dubbing) or isset($subtitles)) {
				return true;
			}
		}
		return false;
	}
	
	public function hasPrices(): bool {
		/** @var Screening $screening */
		foreach($this->toArray() as $screening) {
			$price = $screening->price;
			if(isset($price)) {
				return true;
			}
		}
		return false;
	}
}
