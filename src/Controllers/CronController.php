<?php

namespace App\Controllers;

use App\Entity\Cinema;
use App\Facades\CinemaFacade;
use App\Facades\ParserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Cron controller.
 */
class CronController extends BaseController
{
	/** @var CinemaFacade */
	private $cinemaFacade;
    
	/** @var ParserService */
	private $parserService;
    
	public function __construct(
		CinemaFacade $cinemaFacade,
		ParserService $parserService,
		\Symfony\Contracts\Translation\TranslatorInterface $translator,
		\Symfony\Component\HttpFoundation\RequestStack $requestStack,
		\App\Service\MetaService $metaService
	) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->cinemaFacade = $cinemaFacade;
		$this->parserService = $parserService;
	}
	
	#[Route("/cron", name: "cron_default")]
	public function index(): Response
	{
		return $this->redirectToRoute('homepage');
	}
	
	#[Route("/cron/parse", name: "cron_parse")]
	public function parse(): Response
	{
		$cinemas = $this->cinemaFacade->getParsable();
        
		/** @var Cinema $cinema */
		foreach ($cinemas as $cinema) {
			$this->parserService->initParser($cinema);
            
			$parser = $this->parserService->getParser();
		}
        
		return new Response('Parsing completed');
	}
}
