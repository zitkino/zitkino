<?php

namespace App\Controller;

use App\Entity\Cinema;
use App\Service\CinemaFacade;
use App\Service\ParserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Cron controller.
 */
class CronController extends AbstractController
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
		\App\Service\MetaService $metaService,
		\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag
	) {
		parent::__construct($translator, $requestStack, $metaService, $parameterBag);
		$this->cinemaFacade = $cinemaFacade;
		$this->parserService = $parserService;
	}
	
	#[Route("/cron", name: "cron_default")]
	public function index(): Response
	{
		return $this->redirectToRoute('home_index');
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
