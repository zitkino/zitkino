<?php

namespace App\Controllers;

use App\Models\Cinema\Cinema;
use App\Models\Cinema\CinemaRepository;
use App\Services\{MetaService, ParserService};
use Symfony\Component\HttpFoundation\{RequestStack, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cron controller.
 */
#[Route("/cron", name: "cron_")]
class CronController extends BaseController {
	private CinemaRepository $cinemaRepository;
	
	private ParserService $parserService;
	
	public function __construct(CinemaRepository $cinemaRepository, ParserService $parserService, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaRepository = $cinemaRepository;
		$this->parserService = $parserService;
	}
	
	#[Route("/", name: "index")]
	public function index(): Response {
		return $this->redirectToRoute("homepage");
	}
	
	#[Route("/parse", name: "parse")]
	public function parse(): Response {
		$cinemas = $this->cinemaRepository->grabParsable();
		
		/** @var Cinema $cinema */
		foreach($cinemas as $cinema) {
			$this->parserService->initParser($cinema);
		}
		
		return new Response("Parsing completed");
	}
}
