<?php

namespace App\Controllers;

use App\Models\Facades\CinemaFacade;
use App\Services\MetaService;
use Symfony\Component\HttpFoundation\{RequestStack, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Homepage controller.
 */
class HomeController extends BaseController {
	private CinemaFacade $cinemaFacade;
	
	public function __construct(CinemaFacade $cinemaFacade, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaFacade = $cinemaFacade;
	}
	
	#[Route(path: '/{_locale}', name: 'homepage', requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	public function index(): Response {
		$cinemas = $this->cinemaFacade->gatherWithMovies("current");

		return $this->render('home/index.html.twig', ['cinemas' => $cinemas]);
	}

	#[Route(path: '/mapa', name: 'home_map', requirements: ['_locale' => 'cs'])]
	#[Route(path: '/map', name: 'home_map_en', requirements: ['_locale' => 'en'])]
	public function map(): Response {
		return $this->render('home/map.html.twig');
	}

	#[Route("/kontakt", name: "home_contact", requirements: ['_locale' => 'cs'])]
	#[Route("/contact", name: "home_contact_en", requirements: ['_locale' => 'en'])]
	public function contact(): Response {
		return $this->render('home/contact.html.twig');
	}

	#[Route("/{_locale}/info", name: "home_about", requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
	public function about(): Response {
		return $this->render('home/about.html.twig');
	}
}
