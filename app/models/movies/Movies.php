<?php
namespace Zitkino\Movies;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Movies.
 */
class Movies extends ArrayCollection {
	/** @var Movie[] */
	private $movies;

	/**
	 * @return Movie[]
	 */
	public function getMovies(): array {
		return $this->toArray();
	}
	
	
	public function hasLengths() {
		foreach($this->toArray() as $movie) {
			$length = $movie->length;
			if(isset($length)) { return true; }
		}
		return false;
	}
}
