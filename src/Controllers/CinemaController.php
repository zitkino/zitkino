<?php

namespace App\Controllers;

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
	
	/**
	 *
	 * @param string|int $id
	 */
	#[Route(path: ["cs" => "/kino/{id}", "en" => "/cinema/{id}"], name: "profile")]
	public function profile($id): Response {
		$cinema = $this->cinemaFacade->grabById($id);
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
	
//	#[Route(path: ["cs" => "/klasicka", "en" => "/classic"], name: "type_classic")]
//	#[Route(path: ["/klasicky"], name: "type_classic_alt", alias: ["type_classic"])]
//	#[Route(path: ["cs" => "/multiplexy", "en" => "/multiplexes"], name: "type_multiplex")]
//	#[Route(path: ["/multiplex"], name: "type_multiplex_alt", alias: ["type_multiplex"])]
//	#[Route(path: ["cs" => "/letni", "en" => "/summer"], name: "type_summer")]
//	#[Route(path: ["cs" => "/{slug}", "en" => "/{slug}"], name: "type")]
	public function type(?string $slug = null): Response {
			dump($slug);
			
			$type = $this->cinemaFacade->grabTypeBySlug($slug);
			dump($type);
		// Determine type from route
//		if($type === null) {
//			$route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
//			dump($route);
//			if(str_contains($route, 'classic')) {
//				$type = 'classic';
//			} else if(str_contains($route, 'multiplex')) {
//				$type = 'multiplex';
//			} else if(str_contains($route, 'summer')) {
//				$type = 'summer';
//			}
//		}
		
		return $this->render('cinema/type.html.twig', [
			'cinemas' => $this->cinemaFacade->grabByType($type->getCode()),
			'type' => $type->getCode()
		]);
	}
	
	#[Route(path: ["cs" => "/klasicka/program", "en" => "/classic/programme"], name: "programme_classic")]
	#[Route(path: ["/klasicky/program"], name: "programme_classic_alt", alias: ["programme_classic"])]
	#[Route(path: ["cs" => "/multiplexy/program", "en" => "/multiplexes/programme"], name: "programme_multiplex")]
	#[Route(path: ["/multiplex/program"], name: "programme_multiplex_alt", alias: ["programme_multiplex"])]
	#[Route(path: ["cs" => "/letni/program", "en" => "/summer/programme"], name: "programme_summer")]
	public function programme(?string $type = null): Response {
		dump($type);
		
		// Determine type from route
		if($type === null) {
			$route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
			if(str_contains($route, 'classic')) {
				$type = 'classic';
			} else if(str_contains($route, 'multiplex')) {
				$type = 'multiplex';
			} else if(str_contains($route, 'summer')) {
				$type = 'summer';
			}
		}
		
		return $this->render('cinema/programme.html.twig', [
			'cinemas' => $this->cinemaFacade->gatherWithMovies($type),
			'type' => $type
		]);
	}
}
