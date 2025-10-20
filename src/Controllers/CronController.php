<?php

namespace App\Controllers;

use App\Models\Entities\Cinema;
use App\Models\Facades\CinemaFacade;
use App\Services\MetaService;
use App\Services\ParserService;
use Symfony\Component\HttpFoundation\{RequestStack, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cron controller.
 */
class CronController extends BaseController {
	private CinemaFacade $cinemaFacade;
	
	private ParserService $parserService;
	
	public function __construct(CinemaFacade $cinemaFacade, ParserService $parserService, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaFacade = $cinemaFacade;
		$this->parserService = $parserService;
	}
	
	#[Route("/cron", name: "cron_index")]
	public function index(): Response {
		return $this->redirectToRoute('homepage');
	}
	
	#[Route("/cron/parse", name: "cron_parse")]
	public function parse(): Response {
		$cinemas = $this->cinemaFacade->grabParsable();
		
		/** @var Cinema $cinema */
		foreach($cinemas as $cinema) {
			$this->parserService->initParser($cinema);
			
			$parser = $this->parserService->parser;
		}
		
		return new Response('Parsing completed');
	}
}
