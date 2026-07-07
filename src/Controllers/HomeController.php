<?php

namespace App\Controllers;

use App\Attributes\DatabaseRoute;
use App\Models\Cinema\CinemaRepository;
use App\Models\CinemaType\CinemaTypeRepository;
use App\Models\Page\PageRepository;
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
	private CinemaRepository $cinemaRepository;
	
	private CinemaTypeRepository $cinemaTypeRepository;
	
	private PageRepository $pageRepository;
	
	public function __construct(
		CinemaRepository $cinemaRepository,
		TranslatorInterface $translator,
		RequestStack $requestStack,
		MetaService $metaService,
		PageRepository $pageRepository,
		CinemaTypeRepository $cinemaTypeRepository,
		private readonly CinemaService $cinemaService,
	) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaRepository = $cinemaRepository;
		$this->pageRepository = $pageRepository;
		$this->cinemaTypeRepository = $cinemaTypeRepository;
	}
	
	#[Route(path: "/{_locale}", name: "index", defaults: ["_locale" => "cs"])]
	public function index(): Response {
		$cinemas = $this->cinemaRepository->gatherWithMovies("current");
		$soonestScreenings = $this->cinemaService->getSoonestScreenings();
		$types = $this->cinemaTypeRepository->grabTypes();
		
		return $this->render("home/index.html.twig", [
			"cinemas" => $cinemas,
			"soonestScreenings" => $soonestScreenings,
			"types" => $types,
		]);
	}
	
	#[Route(path: ["cs" => "/mapa", "en" => "/map"], name: "map")]
	public function map(): Response {
		$cinemas = $this->cinemaRepository->grabVisible();
		return $this->render("home/map.html.twig", [
			"cinemas" => $cinemas,
			"google_maps_key" => $this->getParameter("google-maps-key"),
		]);
	}
	
	#[Route(path: ["cs" => "/kontakt", "en" => "/contact"], name: "contact")]
	public function contact(): Response {
		return $this->render("home/contact.html.twig");
	}
	
	#[DatabaseRoute(path: null, name: "about_", entityClass: "App\Models\Page\Page")]
//	#[Route(path: ["cs" => "/informace", "en" => "/information"], name: "about_alt", alias: ["home_about"])]
	public function about(): Response {
		$page = $this->pageRepository->grabByIdent("info");
		return $this->render("home/about.html.twig", ["page" => $page]);
	}
}
