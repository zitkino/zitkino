<?php

namespace App\Services;

use App\Models\Cinema\Cinema;
use App\Models\Movie\Movie;
use App\Models\Place\Place;
use App\Models\Screening\Screening;
use App\Models\ScreeningFormat\ScreeningFormat;
use App\Models\ScreeningType\ScreeningType;
use App\Models\Movie\MovieRepository;
use App\Models\Place\PlaceRepository;
use App\Models\ScreeningFormat\ScreeningFormatRepository;
use App\Models\Screening\ScreeningRepository;
use App\Models\ScreeningType\ScreeningTypeRepository;

class BuilderService {
	public bool $save = true;
	
	public function __construct(private readonly MovieRepository $movieRepository, private readonly PlaceRepository $placeRepository, private readonly ScreeningRepository $screeningRepository, private readonly ScreeningFormatRepository $screeningFormatRepository, private readonly ScreeningTypeRepository $screeningTypeRepository) {
	}
	
	public function movie(string $name, ?int $length = null, ?string $csfd = null, ?string $imdb = null): Movie {
		$movie = $this->movieRepository->grabByName($name);
		if(!isset($movie)) {
			$movie = new Movie($name);
			$movie->length = $length;
			$movie->csfd = $csfd;
			$movie->imdb = $imdb;
		}
		
		if($this->save) {
			$this->movieRepository->save($movie);
		}
		
		return $movie;
	}
	
	public function place(string $name, Cinema $cinema, ?string $link = null): Place {
		$place = $this->placeRepository->grabByName($name);
		if(!isset($place)) {
			$place = new Place($name);
			$place->cinema = $cinema;
			$place->link = $link;
		}
		
		if($this->save) {
			$this->placeRepository->save($place);
		}
		
		return $place;
	}
	
	public function screeningFormat(?string $name = null): ?ScreeningFormat {
		if(empty($name)) {
			return null;
		}
		
		$format = $this->screeningFormatRepository->grabFormat($name);
		if(!isset($format)) {
			$format = new ScreeningFormat($name);
		}
		
		if($this->save) {
			$this->screeningFormatRepository->save($format);
		}
		
		return $format;
	}
	
	public function screeningType(?string $name = null): ?ScreeningType {
		if(empty($name)) {
			return null;
		}
		
		$type = $this->screeningTypeRepository->grabType($name);
		if(!isset($type)) {
			$type = new ScreeningType($name);
		}
		
		if($this->save) {
			$this->screeningTypeRepository->save($type);
		}
		
		return $type;
	}
	
	public function screening(Cinema $cinema, Movie $movie, ?Place $place = null, ScreeningFormat|string|null $format = null, ScreeningType|string|null $type = null, ?string $dubbing = null, ?string $subtitles = null, ?int $price = null, ?string $link = null, array $showtimes = []): Screening {
		$screening = new Screening($cinema, $movie);
		$screening->place = $place;
		$screening->dubbing = $dubbing;
		$screening->subtitles = $subtitles;
		$screening->price = $price;
		$screening->link = $link;
		$screening->setShowtimes($showtimes);
		
		if($format instanceof ScreeningFormat) {
			$formatEntity = $format;
		} else {
			$formatEntity = $this->screeningFormat($format);
		}
		$screening->format = $formatEntity;
		
		if($type instanceof ScreeningType) {
			$typeEntity = $type;
		} else {
			$typeEntity = $this->screeningType($type);
		}
		$screening->type = $typeEntity;
		
		if($this->save) {
			$this->screeningRepository->save($screening);
		}
		
		return $screening;
	}
}
