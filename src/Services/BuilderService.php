<?php

namespace App\Services;

use App\Models\Entities\{Cinema, Movie, Place, Screening, ScreeningFormat, ScreeningType};
use App\Models\Facades\{MovieFacade, PlaceFacade, ScreeningFacade};

class BuilderService {
	public bool $save = true;
	
	public function __construct(private readonly MovieFacade $movieFacade, private readonly PlaceFacade $placeFacade, private readonly ScreeningFacade $screeningFacade) {
	}
	
	public function movie(string $name, ?int $length = null, ?string $csfd = null, ?string $imdb = null): Movie {
		$movie = $this->movieFacade->grabByName($name);
		if(!isset($movie)) {
			$movie = new Movie($name);
			$movie->setLength($length)
				->setCsfd($csfd)
				->setImdb($imdb);
		}
		
		if($this->save) {
			$this->movieFacade->save($movie);
		}
		
		return $movie;
	}
	
	public function place(string $name, Cinema $cinema, ?string $link = null): Place {
		$place = $this->placeFacade->grabByName($name);
		if(!isset($place)) {
			$place = new Place($name);
			$place->setCinema($cinema)
				->setLink($link);
		}
		
		if($this->save) {
			$this->placeFacade->save($place);
		}
		
		return $place;
	}
	
	public function screeningFormat(?string $name = null): ?ScreeningFormat {
		if(empty($name)) {
			return null;
		}
		
		$format = $this->screeningFacade->grabFormat($name);
		if(!isset($format)) {
			$format = new ScreeningFormat($name);
		}
		
		if($this->save) {
			$this->screeningFacade->save($format);
		}
		
		return $format;
	}
	
	public function screeningType(?string $name = null): ?ScreeningType {
		if(empty($name)) {
			return null;
		}
		
		$type = $this->screeningFacade->grabType($name);
		if(!isset($type)) {
			$type = new ScreeningType($name);
		}
		
		if($this->save) {
			$this->screeningFacade->save($type);
		}
		
		return $type;
	}
	
	public function screening(Cinema $cinema, Movie $movie, ?Place $place = null, ScreeningFormat|string|null $format = null, ScreeningType|string|null $type = null, ?string $dubbing = null, ?string $subtitles = null, ?int $price = null, ?string $link = null, array $showtimes = []): Screening {
		$screening = new Screening($cinema, $movie);
		
		$screening->setPlace($place)
			->setLanguages($dubbing, $subtitles)
			->setPrice($price)
			->setLink($link)
			->setShowtimes($showtimes);
		
		if($format instanceof ScreeningFormat) {
			$formatEntity = $format;
		} else {
			$formatEntity = $this->screeningFormat($format);
		}
		$screening->setFormat($formatEntity);
		
		if($type instanceof ScreeningType) {
			$typeEntity = $type;
		} else {
			$typeEntity = $this->screeningType($type);
		}
		$screening->setType($typeEntity);
		
		if($this->save) {
			$this->screeningFacade->save($screening);
		}
		
		return $screening;
	}
}
