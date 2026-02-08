<?php

namespace App\Controllers;

use App\Models\Entities\Cinema;
use App\Models\Facades\CinemaFacade;
use App\Services\{MetaService, ParserService};
use Symfony\Component\HttpFoundation\{RequestStack, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cron controller.
 */
#[Route("/cron", name: "cron_")]
class CronController extends BaseController {
	private CinemaFacade $cinemaFacade;
	
	private ParserService $parserService;
	
	public function __construct(CinemaFacade $cinemaFacade, ParserService $parserService, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaFacade = $cinemaFacade;
		$this->parserService = $parserService;
	}
	
	#[Route("/", name: "index")]
	public function index(): Response {
		return $this->redirectToRoute("homepage");
	}
	
	#[Route("/parse", name: "parse")]
	public function parse(): Response {
		$cinemas = $this->cinemaFacade->grabParsable();
		
		/** @var Cinema $cinema */
		foreach($cinemas as $cinema) {
			$this->parserService->initParser($cinema);
		}
		
		return new Response("Parsing completed");
	}
}
