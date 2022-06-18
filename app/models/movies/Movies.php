<?php
namespace Zitkino\Movies;

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
