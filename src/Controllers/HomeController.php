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
#[Route("/", name: "home_")]
class HomeController extends BaseController {
	private CinemaFacade $cinemaFacade;
	
	public function __construct(CinemaFacade $cinemaFacade, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaFacade = $cinemaFacade;
	}
	
	#[Route(path: '/{_locale}', name: 'index', defaults: ['_locale' => 'cs'])]
	public function index(): Response {
		$cinemas = $this->cinemaFacade->gatherWithMovies("current");
		
		return $this->render('home/index.html.twig', ['cinemas' => $cinemas]);
	}
	
	#[Route(path: ['cs' => '/mapa', 'en' => '/map'], name: 'map')]
	public function map(): Response {
		return $this->render('home/map.html.twig');
	}
	
	#[Route(path: ["cs" => "/kontakt", "en" => "/contact"], name: "contact")]
	public function contact(): Response {
		return $this->render('home/contact.html.twig');
	}
	
	#[Route(path: ["cs" => "/info", 'en' => "/about"], name: "about")]
//	#[Route(path: ["cs" => "/informace", 'en' => "/information"], name: "about_alt", alias: ["home_about"])]
	public function about(): Response {
		return $this->render('home/about.html.twig');
	}
}
