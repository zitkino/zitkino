<?php
namespace App\Services;

use App\Logging\FileLoggerFactory;
use App\Models\Cinema\Cinema;
use App\Models\Cinema\CinemaRepository;
use App\Models\Screening\ScreeningRepository;
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
	
	public CinemaRepository $cinemaRepository {
		get {
			return $this->cinemaRepository;
		}
	}
	
	public ScreeningRepository $screeningRepository {
		get {
			return $this->screeningRepository;
		}
	}
	
	private FileLoggerFactory $logger;
	
	public function __construct(public readonly HttpClientInterface $httpClient, public readonly BuilderService $builderService, CinemaRepository $cinemaRepository, ScreeningRepository $screeningRepository, FileLoggerFactory $logger) {
		$this->cinemaRepository = $cinemaRepository;
		$this->screeningRepository = $screeningRepository;
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
				$this->screeningRepository->removeScreenings($cinema);
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
