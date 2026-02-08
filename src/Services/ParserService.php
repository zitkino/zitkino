<?php
namespace App\Services;

use App\Logging\FileLoggerFactory;
use App\Models\Entities\Cinema;
use App\Models\Facades\{CinemaFacade, ScreeningFacade};
use App\Parsers\Parser;
use Monolog\Attribute\WithMonologChannel;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[WithMonologChannel("parsers")]
class ParserService {
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
	
	public ScreeningFacade $screeningFacade {
		get {
			return $this->screeningFacade;
		}
	}
	
	private FileLoggerFactory $logger;
	
	public function __construct(public readonly HttpClientInterface $httpClient, public readonly BuilderService $builderService, CinemaFacade $cinemaFacade, ScreeningFacade $screeningFacade, FileLoggerFactory $logger) {
		$this->cinemaFacade = $cinemaFacade;
		$this->screeningFacade = $screeningFacade;
		$this->logger = $logger;
	}
	
	public function initParser(Cinema $cinema): void {
//		$cinemaLogger = $this->logger->pushHandler(new StreamHandler(__DIR__."/".$cinema->getCode().".log", Level::Debug, false));
		$cinemaLogger = $this->logger->filename($cinema->getIdent());
		
		try {
			$parserName = ucfirst($cinema->getIdent());
			$parserClass = "\App\Parsers\\".$parserName;
			if(class_exists($parserClass)) {
				$this->parser = new $parserClass($this, $cinema);
				$this->screeningFacade->removeScreenings($cinema);
				$this->parser->parse();
			} else {
				$cinemaLogger->warning(sprintf("Parser class %s does not exist for cinema %s", $parserClass, $parserName));
			}
		} catch(\Error $error) {
			dump($error);
			$cinemaLogger->error($error->getMessage(), ["error" => $error]);
		} catch(\Exception $exception) {
			dump($exception);
			$cinemaLogger->error($exception->getMessage(), ["exception" => $exception]);
		}
	}
}
