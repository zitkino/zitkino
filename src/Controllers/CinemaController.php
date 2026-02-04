<?php

namespace App\Controllers;

use App\Attributes\DatabaseRoute;
use App\Models\Facades\CinemaFacade;
use App\Services\MetaService;
use Symfony\Component\HttpFoundation\{RequestStack, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cinema controller.
 */
#[Route(name: "cinema_")]
class CinemaController extends BaseController {
	private CinemaFacade $cinemaFacade;
	
	public function __construct(CinemaFacade $cinemaFacade, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaFacade = $cinemaFacade;
	}
	
	#[Route(path: ["cs" => "/kina", "en" => "/cinemas"], name: "default")]
	public function index(): Response {
		return $this->render('cinema/default.html.twig', [
			"types" => $this->cinemaFacade->grabTypes(),
			'classicCinemas' => $this->cinemaFacade->grabByType("classic"),
			'multiplexCinemas' => $this->cinemaFacade->grabByType("multiplex"),
			'summerCinemas' => $this->cinemaFacade->grabByType("summer")
		]);
	}
	
	#[DatabaseRoute(
		path: ["cs" => "/kino/{slug}", "en" => "/cinema/{slug}"], name: "profile_",
		entityClass: "App\Models\Entities\Cinema"
	)]
	public function profile($slug): Response {
		$cinema = $this->cinemaFacade->grabBySlug($slug);
		$screenings = $cinema->getNewScreenings();
		
		$gmaps = $cinema->getGmaps();
		if($gmaps === null) {
			$address = $cinema->getAddress().", ".$cinema->getCity();
			$param = urlencode($address);
		} else {
			$param = "place_id:".$gmaps;
		}
		
		return $this->render('cinema/profile.html.twig', [
			'cinema' => $cinema,
			'screenings' => $screenings,
			'gmap' => $param,
			'gmapKey' => $this->getParameter('google-maps-key')
		]);
	}
	
	#[DatabaseRoute(
		path: ["cs" => "/{slug}", "en" => "/{slug}"], name: "type_",
		entityClass: "App\Models\Entities\CinemaType"
	)]
	public function type(?string $slug = null): Response {
		$type = $this->cinemaFacade->grabTypeBySlug($slug);
		return $this->render('cinema/type.html.twig', [
			'cinemas' => $this->cinemaFacade->grabByType($type->getIdent()),
			'type' => $type
		]);
	}
	
	#[DatabaseRoute(
		path: ["cs" => "/{slug}/program", "en" => "/{slug}/programme"], name: "programme_", entityClass: "App\Models\Entities\CinemaType"
	)]
	public function programme(?string $slug = null): Response {
		$type = $this->cinemaFacade->grabTypeBySlug($slug);
		
		return $this->render('cinema/programme.html.twig', [
			'cinemas' => $this->cinemaFacade->gatherWithMovies($type->getIdent()),
			'type' => $type
		]);
	}
}
