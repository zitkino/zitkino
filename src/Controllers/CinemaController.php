<?php

namespace App\Controllers;

use App\Facades\CinemaFacade;
use App\Services\MetaService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cinema controller.
 */
class CinemaController extends BaseController {
	/** @var CinemaFacade */
	private $cinemaFacade;
	
	public function __construct(CinemaFacade $cinemaFacade, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaFacade = $cinemaFacade;
	}
	
	#[Route("/kina", name: "cinema_default")]
	#[Route("/kino", name: "cinema_default_alt")]
	public function index(): Response {
		return $this->render('cinema/default.html.twig', [
			'classicCinemas' => $this->cinemaFacade->grabByType("classic"),
			'multiplexCinemas' => $this->cinemaFacade->grabByType("multiplex"),
			'summerCinemas' => $this->cinemaFacade->grabByType("summer")
		]);
	}
	
	/**
	 *
	 * @param string|int $id
	 */
	#[Route("/kino/{id}", name: "cinema_profile")]
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
	
	#[Route("/klasicka", name: "cinema_type_classic")]
	#[Route("/klasicky", name: "cinema_type_classic_alt")]
	#[Route("/multiplexy", name: "cinema_type_multiplex")]
	#[Route("/multiplex", name: "cinema_type_multiplex_alt")]
	#[Route("/letni", name: "cinema_type_summer")]
	public function type(?string $type = null): Response {
		// Determine type from route
		if($type === null) {
			$route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
			if(strpos($route, 'classic') !== false) {
				$type = 'classic';
			} else if(strpos($route, 'multiplex') !== false) {
				$type = 'multiplex';
			} else if(strpos($route, 'summer') !== false) {
				$type = 'summer';
			}
		}
		
		return $this->render('cinema/type.html.twig', [
			'cinemas' => $this->cinemaFacade->grabByType($type),
			'type' => $type
		]);
	}
	
	/**
	 *
	 * @param string $type
	 */
	#[Route("/klasicky/program", name: "cinema_programme_classic")]
	#[Route("/klasicka/program", name: "cinema_programme_classic_alt")]
	#[Route("/multiplexy/program", name: "cinema_programme_multiplex")]
	#[Route("/multiplex/program", name: "cinema_programme_multiplex_alt")]
	#[Route("/letni/program", name: "cinema_programme_summer")]
	public function programme(string $type = null): Response {
		// Determine type from route
		if($type === null) {
			$route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
			if(strpos($route, 'classic') !== false) {
				$type = 'classic';
			} else if(strpos($route, 'multiplex') !== false) {
				$type = 'multiplex';
			} else if(strpos($route, 'summer') !== false) {
				$type = 'summer';
			}
		}
		
		return $this->render('cinema/programme.html.twig', [
			'cinemas' => $this->cinemaFacade->gatherWithMovies($type),
			'type' => $type
		]);
	}
}
