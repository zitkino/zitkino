<?php
namespace Zitkino\Parsers;

use Nettrine\ORM\EntityManagerDecorator;
use Tracy\Debugger;
use Zitkino\Cinemas\Cinema;
use Zitkino\Cinemas\CinemaFacade;
use Zitkino\LanguageFacade;
use Zitkino\MovieFacade;
use Zitkino\PlaceFacade;
use Zitkino\ScreeningFacade;

class ParserService {
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
	
	/** @var EntityManagerDecorator */
	private $entityManager;
	
	/**
	 * ParserService constructor.
	 * @param EntityManagerDecorator $entityManager
	 * @param CinemaFacade $cinemaFacade
	 * @param LanguageFacade $languageFacade
	 * @param MovieFacade $movieFacade
	 * @param PlaceFacade $placeFacade
	 * @param ScreeningFacade $screeningFacade
	 */
	public function __construct(EntityManagerDecorator $entityManager, CinemaFacade $cinemaFacade, LanguageFacade $languageFacade, MovieFacade $movieFacade, PlaceFacade $placeFacade, ScreeningFacade $screeningFacade) {
		$this->cinemaFacade = $cinemaFacade;
		$this->languageFacade = $languageFacade;
		$this->movieFacade = $movieFacade;
		$this->placeFacade = $placeFacade;
		$this->screeningFacade = $screeningFacade;
		$this->entityManager = $entityManager;
	}
	
	/**
	 * @return Parser
	 */
	public function getParser(): Parser {
		return $this->parser;
	}
	
	/**
	 * @param Parser $parser
	 * @return ParserService
	 */
	public function setParser(Parser $parser): ParserService {
		$this->parser = $parser;
		return $this;
	}
	
	public function initParser(Cinema $cinema) {
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
	
	/**
	 * @return EntityManagerDecorator
	 */
	public function getEntityManager(): EntityManagerDecorator {
		return $this->entityManager;
	}
	
	/**
	 * @return CinemaFacade
	 */
	public function getCinemaFacade(): CinemaFacade {
		return $this->cinemaFacade;
	}
	
	/**
	 * @return LanguageFacade
	 */
	public function getLanguageFacade(): LanguageFacade {
		return $this->languageFacade;
	}
	
	/**
	 * @return MovieFacade
	 */
	public function getMovieFacade(): MovieFacade {
		return $this->movieFacade;
	}
	
	/**
	 * @return PlaceFacade
	 */
	public function getPlaceFacade(): PlaceFacade {
		return $this->placeFacade;
	}
	
	/**
	 * @return ScreeningFacade
	 */
	public function getScreeningFacade(): ScreeningFacade {
		return $this->screeningFacade;
	}
}
