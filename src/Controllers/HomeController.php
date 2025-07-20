<?php

namespace App\Controllers;

use App\Facades\CinemaFacade;
use App\Service\MetaService;
use Symfony\Component\HttpFoundation\{RequestStack, Response};
use Symfony\Component\Routing\Annotation\Route;
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
	
	#[Route("/", name: "homepage")]
	public function index(): Response {
		$cinemas = $this->cinemaFacade->getWithMovies("current");
		
		return $this->render('home/index.html.twig', ['cinemas' => $cinemas]);
	}
	
	#[Route("/mapa", name: "home_map")]
	public function map(): Response {
		return $this->render('home/map.html.twig');
	}
	
	#[Route("/kontakt", name: "home_contact")]
	public function contact(): Response {
		return $this->render('home/contact.html.twig');
	}
	
	#[Route("/info", name: "home_about")]
	public function about(): Response {
		return $this->render('home/about.html.twig');
	}
}
