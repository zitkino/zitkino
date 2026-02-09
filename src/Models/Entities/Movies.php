<?php
namespace App\Models\Entities;

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
			if(isset($movie->length)) {
				return true;
			}
		}
		return false;
	}
}
