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
class CinemaController extends BaseController {
	private CinemaFacade $cinemaFacade;
	
	public function __construct(CinemaFacade $cinemaFacade, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaFacade = $cinemaFacade;
	}
	
	#[Route("/kina", name: "cinema_default", requirements: ['_locale' => 'cs'])]
	#[Route("/cinemas", name: "cinema_default_en", requirements: ['_locale' => 'en'])]
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
	#[Route("/cinema/{id}", name: "cinema_profile_en", locale: 'en')]
	#[Route("/kino/{id}", name: "cinema_profile", locale: 'cs')]
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
	
	#[Route("/{_locale}/klasicka", name: "cinema_type_classic", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	#[Route("/{_locale}/klasicky", name: "cinema_type_classic_alt", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	#[Route("/{_locale}/multiplexy", name: "cinema_type_multiplex", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	#[Route("/{_locale}/multiplex", name: "cinema_type_multiplex_alt", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	#[Route("/{_locale}/letni", name: "cinema_type_summer", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
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
	
	#[Route("/{_locale}/klasicky/program", name: "cinema_programme_classic", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	#[Route("/{_locale}/klasicka/program", name: "cinema_programme_classic_alt", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	#[Route("/{_locale}/multiplexy/program", name: "cinema_programme_multiplex", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	#[Route("/{_locale}/multiplex/program", name: "cinema_programme_multiplex_alt", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	#[Route("/{_locale}/letni/program", name: "cinema_programme_summer", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	public function programme(?string $type = null): Response {
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
