<?php

namespace App\Controllers;

use App\Attributes\DatabaseRoute;
use App\Models\Facades\{CinemaFacade, PageFacade};
use App\Services\CinemaService;
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
	
	private PageFacade $pageFacade;
	
	public function __construct(
		CinemaFacade $cinemaFacade,
		TranslatorInterface $translator,
		RequestStack $requestStack,
		MetaService $metaService,
		PageFacade $pageFacade,
		private readonly CinemaService $cinemaService,
	) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaFacade = $cinemaFacade;
		$this->pageFacade = $pageFacade;
	}
	
	#[Route(path: "/{_locale}", name: "index", defaults: ["_locale" => "cs"])]
	public function index(): Response {
		$cinemas = $this->cinemaFacade->gatherWithMovies("current");
		$soonestScreenings = $this->cinemaService->getSoonestScreenings();
		$types = $this->cinemaFacade->grabTypes();
		
		return $this->render("home/index.html.twig", [
			"cinemas" => $cinemas,
			"soonestScreenings" => $soonestScreenings,
			"types" => $types,
		]);
	}
	
	#[Route(path: ["cs" => "/mapa", "en" => "/map"], name: "map")]
	public function map(): Response {
		$cinemas = $this->cinemaFacade->grabVisible();
		return $this->render("home/map.html.twig", [
			"cinemas" => $cinemas,
			"google_maps_key" => $this->getParameter("google-maps-key"),
		]);
	}
	
	#[Route(path: ["cs" => "/kontakt", "en" => "/contact"], name: "contact")]
	public function contact(): Response {
		return $this->render("home/contact.html.twig");
	}
	
	#[DatabaseRoute(path: null, name: "about_", entityClass: "App\Models\Entities\Page")]
//	#[Route(path: ["cs" => "/informace", "en" => "/information"], name: "about_alt", alias: ["home_about"])]
	public function about(): Response {
		$page = $this->pageFacade->grabByIdent("info");
		return $this->render("home/about.html.twig", ["page" => $page]);
	}
}
