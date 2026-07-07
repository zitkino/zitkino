<?php

namespace App\Controllers;

use App\Attributes\DatabaseRoute;
use App\Models\Cinema\CinemaRepository;
use App\Models\CinemaType\CinemaTypeRepository;
use App\Services\MetaService;
use Symfony\Component\HttpFoundation\{RequestStack, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cinema controller.
 */
#[Route(name: "cinema_")]
class CinemaController extends BaseController {
	private CinemaRepository $cinemaRepository;
	
	private CinemaTypeRepository $cinemaTypeRepository;
	
	public function __construct(CinemaRepository $cinemaRepository, CinemaTypeRepository $cinemaTypeRepository, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaRepository = $cinemaRepository;
		$this->cinemaTypeRepository = $cinemaTypeRepository;
	}
	
	#[Route(path: ["cs" => "/kina", "en" => "/cinemas"], name: "default")]
	public function index(): Response {
		return $this->render("cinema/default.html.twig", [
			"types" => $this->cinemaTypeRepository->grabTypes(),
			"classicCinemas" => $this->cinemaRepository->grabByType("classic"),
			"multiplexCinemas" => $this->cinemaRepository->grabByType("multiplex"),
			"summerCinemas" => $this->cinemaRepository->grabByType("summer")
		]);
	}
	
	#[DatabaseRoute(path: ["cs" => "/kino/{slug}", "en" => "/cinema/{slug}"], name: "profile_", entityClass: "App\Models\Cinema\Cinema")]
	public function profile($slug): Response {
		$cinema = $this->cinemaRepository->grabBySlug($slug);
		$screenings = $cinema->getNewScreenings();
		
		$gmaps = $cinema->gmaps;
		if($gmaps === null) {
			$address = $cinema->address.", ".$cinema->city;
			$param = urlencode($address);
		} else {
			$param = "place_id:".$gmaps;
		}
		
		return $this->render("cinema/profile.html.twig", [
			"cinema" => $cinema,
			"screenings" => $screenings,
			"gmap" => $param,
			"gmapKey" => $this->getParameter("google-maps-key")
		]);
	}
	
	#[DatabaseRoute(path: ["cs" => "/{slug}", "en" => "/{slug}"], name: "type_", entityClass: "App\Models\CinemaType\CinemaType")]
	public function type(?string $slug = null): Response {
		$type = $this->cinemaTypeRepository->grabTypeBySlug($slug);
		return $this->render("cinema/type.html.twig", [
			"cinemas" => $this->cinemaRepository->grabByType($type->getIdent()),
			"type" => $type
		]);
	}
	
	#[DatabaseRoute(path: ["cs" => "/{slug}/program", "en" => "/{slug}/programme"], name: "programme_", entityClass: "App\Models\CinemaType\CinemaType")]
	public function programme(?string $slug = null): Response {
		$type = $this->cinemaTypeRepository->grabTypeBySlug($slug);
		
		return $this->render("cinema/programme.html.twig", [
			"cinemas" => $this->cinemaRepository->gatherWithMovies($type->getIdent()),
			"type" => $type
		]);
	}
}
