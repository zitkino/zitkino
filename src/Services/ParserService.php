<?php
namespace App\Services;

use App\Logging\FileLoggerFactory;
use App\Models\Entities\Cinema;
use App\Models\Facades\{LanguageFacade, CinemaFacade, MovieFacade, PlaceFacade, ScreeningFacade};
use App\Parsers\Parser;
use Monolog\Attribute\WithMonologChannel;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[WithMonologChannel('parsers')]
class ParserService {
	private HttpClientInterface $httpClient;
	
	public Parser $parser {
		get {
			return $this->parser;
		}
	}
	
	public CinemaFacade $cinemaFacade {
		get {
			return $this->cinemaFacade;
		}
	}
	
	private LanguageFacade $languageFacade {
		get {
			return $this->languageFacade;
		}
	}
	
	public MovieFacade $movieFacade {
		get {
			return $this->movieFacade;
		}
	}
	
	public PlaceFacade $placeFacade {
		get {
			return $this->placeFacade;
		}
	}
	
	public ScreeningFacade $screeningFacade {
		get {
			return $this->screeningFacade;
		}
	}
	
	private FileLoggerFactory $logger;
	
	public function __construct(HttpClientInterface $httpClient, CinemaFacade $cinemaFacade, LanguageFacade $languageFacade, MovieFacade $movieFacade, PlaceFacade $placeFacade, ScreeningFacade $screeningFacade, FileLoggerFactory $logger) {
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
	
	public function initParser(Cinema $cinema): void {
//		$cinemaLogger = $this->logger->pushHandler(new StreamHandler(__DIR__.'/'.$cinema->getCode().".log", Level::Debug, false));
		$cinemaLogger = $this->logger->filename($cinema->getIdent());
		
		try {
			$parserClass = "\App\Parsers\\".ucfirst($cinema->getIdent());
			if(class_exists($parserClass)) {
				$this->parser = new $parserClass($this, $cinema);
				$this->screeningFacade->removeScreenings($cinema);
				$this->parser->parse();
			} else {
				$cinemaLogger->error(sprintf("Parser class %s does not exist for cinema %s", $parserClass, $cinema->getIdent()));
			}
		} catch(\Error $error) {
			dump($error);
			$cinemaLogger->error(sprintf("Error initializing parser for cinema %s: %s", $cinema->getIdent(), $error->getMessage()), ['error' => $error]);
		} catch(\Exception $exception) {
			dump($exception);
			$cinemaLogger->error(sprintf("Error initializing parser for cinema %s: %s", $cinema->getIdent(), $exception->getMessage()), ['exception' => $exception]);
		}
	}
}
