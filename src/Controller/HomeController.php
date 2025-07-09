<?php

namespace App\Controller;

use App\Service\CinemaFacade;
use App\Service\MetaService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Homepage controller.
 */
class HomeController extends AbstractController {
	private CinemaFacade $cinemaFacade;
	
	public function __construct(
		CinemaFacade $cinemaFacade,
		TranslatorInterface $translator,
		RequestStack $requestStack,
		MetaService $metaService,
		ParameterBagInterface $parameterBag
	) {
		parent::__construct($translator, $requestStack, $metaService, $parameterBag);
		$this->cinemaFacade = $cinemaFacade;
	}
	
	#[Route("/", name: "home_index")]
	public function index(): Response {
		$cinemas = $this->cinemaFacade->getWithMovies("current");
		
		return $this->render('home/index.html.twig', [
			'cinemas' => $cinemas
		]);
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
