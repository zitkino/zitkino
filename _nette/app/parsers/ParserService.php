<?php
namespace _nette\app\parsers;

use _nette\app\Models\cinemas\Cinema;
use _nette\app\parsers\Parser;
use Contributte\Guzzlette\ClientFactory;
use Tracy\Debugger;
use _nette\app\Models\cinemas\{CinemaFacade};
use _nette\app\Models\languages\LanguageFacade;
use _nette\app\Models\movies\MovieFacade;
use _nette\app\Models\places\PlaceFacade;
use _nette\app\Models\screenings\ScreeningFacade;

class ParserService {
	/** @var ClientFactory */
	private $clientFactory;
	
	/** @var Parser */
	private $parser;
	
	/** @var CinemaFacade */
	private $cinemaFacade;
	
	/** @var LanguageFacade */
	private $languageFacade;
	
	/** @var MovieFacade */
	private $movieFacade;
	
	/** @var PlaceFacade */
	private $placeFacade;
	
	/** @var ScreeningFacade */
	private $screeningFacade;
	
	public function __construct(ClientFactory $clientFactory, CinemaFacade $cinemaFacade, LanguageFacade $languageFacade, MovieFacade $movieFacade, PlaceFacade $placeFacade, ScreeningFacade $screeningFacade) {
		$this->clientFactory = $clientFactory;
		$this->cinemaFacade = $cinemaFacade;
		$this->languageFacade = $languageFacade;
		$this->movieFacade = $movieFacade;
		$this->placeFacade = $placeFacade;
		$this->screeningFacade = $screeningFacade;
	}
	
	public function getClientFactory(): ClientFactory {
		return $this->clientFactory;
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
			$parserClass = "\Zitkino\Parsers\\".ucfirst($cinema->getCode());
			if(class_exists($parserClass)) {
				$this->parser = new $parserClass($this, $cinema);
				$this->getScreeningFacade()->removeScreenings($cinema);
				$this->parser->parse();
			}
		} catch(\Error $error) {
			Debugger::barDump($error);
			Debugger::log($error, Debugger::ERROR);
		} catch(\Exception $exception) {
			Debugger::barDump($exception);
			Debugger::log($exception, Debugger::EXCEPTION);
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
