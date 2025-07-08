<?php
namespace _nette\app\Models\movies;

use _nette\app\Models\movies\Movie;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Movies.
 */
class Movies extends ArrayCollection {
	public function getMovies(): array {
		return $this->toArray();
	}
	
	public function hasLengths(): bool {
		/** @var Movie $movie */
		foreach($this->toArray() as $movie) {
			$length = $movie->getLength();
			if(isset($length)) {
				return true;
			}
		}
		return false;
	}
}
