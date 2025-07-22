<?php
namespace App\Services;

use App\Entities\Cinema;
use App\Facades\{CinemaFacade, LanguageFacade, MovieFacade, PlaceFacade, ScreeningFacade};
use App\Parsers\Parser;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParserService {
	private HttpClientInterface $httpClient;
	
	private Parser $parser;
	
	private CinemaFacade $cinemaFacade;
	
	private LanguageFacade $languageFacade;
	
	private MovieFacade $movieFacade;
	
	private PlaceFacade $placeFacade;
	
	private ScreeningFacade $screeningFacade;
	
	private LoggerInterface $logger;
	
	public function __construct(HttpClientInterface $httpClient, CinemaFacade $cinemaFacade, LanguageFacade $languageFacade, MovieFacade $movieFacade, PlaceFacade $placeFacade, ScreeningFacade $screeningFacade, LoggerInterface $logger) {
		$this->httpClient = $httpClient;
		$this->cinemaFacade = $cinemaFacade;
		$this->languageFacade = $languageFacade;
		$this->movieFacade = $movieFacade;
		$this->placeFacade = $placeFacade;
		$this->screeningFacade = $screeningFacade;
		$this->logger = $logger;
	}
	
	public function getHttpClient(): HttpClientInterface {
		return $this->httpClient;
	}
	
	public function getParser(): Parser {
		return $this->parser;
	}
	
	public function setParser(Parser $parser): ParserService {
		$this->parser = $parser;
		return $this;
	}
	
	public function initParser(Cinema $cinema): void {
		try {
			$parserClass = "\App\Parsers\\".ucfirst($cinema->getCode());
			if(class_exists($parserClass)) {
				$this->parser = new $parserClass($this, $cinema);
				$this->getScreeningFacade()
						->removeScreenings($cinema);
				$this->parser->parse();
			}
		} catch(\Error $error) {
			dump($error);
			$this->logger->error(sprintf("Error initializing parser for cinema %s: %s", $cinema->getCode(), $error->getMessage()));
		} catch(\Exception $exception) {
			dump($exception);
			$this->logger->error(sprintf("Error initializing parser for cinema %s: %s", $cinema->getCode(), $exception->getMessage()));
		}
	}
	
	public function getCinemaFacade(): CinemaFacade {
		return $this->cinemaFacade;
	}
	
	public function getLanguageFacade(): LanguageFacade {
		return $this->languageFacade;
	}
	
	public function getMovieFacade(): MovieFacade {
		return $this->movieFacade;
	}
	
	public function getPlaceFacade(): PlaceFacade {
		return $this->placeFacade;
	}
	
	public function getScreeningFacade(): ScreeningFacade {
		return $this->screeningFacade;
	}
}
