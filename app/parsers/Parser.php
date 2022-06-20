<?php
namespace Zitkino\Parsers;

use Doctrine\DBAL\Connection;
use GuzzleHttp\Exception\GuzzleException;
use Nette\Utils\{Json, JsonException};
use Zitkino\Cinemas\Cinema;
use Zitkino\Exceptions\ParserException;

/**
 * Parser.
 */
abstract class Parser {
	/** @var Cinema */
	protected $cinema;
	
	/** @var string|null */
	private $url;
	
	/** @var Connection */
	protected $connection;
	
	/** @var ParserService */
	protected $parserService;
	
	public function __construct(ParserService $parserService, Cinema $cinema) {
		$this->parserService = $parserService;
		$this->cinema = $cinema;
		$this->url = $this->cinema->getParsing();
	}
	
	public function getUrl(): string {
		return $this->url;
	}
	
	public function setUrl(string $url): Parser {
		$this->url = $url;
		return $this;
	}
	
	/**
	 * Downloads data from internet.
	 * @throws ParserException
	 */
	private function downloadData(): string {
		try {
			$response = $this->parserService->getClientFactory()->createClient()->get($this->url);
			$body = (string)$response->getBody();
		} catch(GuzzleException $e) {
			$e = new ParserException($e->getMessage());
			$e->setUrl($this->getUrl());
			throw $e;
		}
		
		return $body ?: "";
	}
	
	/**
	 * @throws ParserException
	 */
	public function getXpath(): \DOMXPath {
		$data = $this->downloadData();
		libxml_use_internal_errors(true); // Prevent HTML errors from displaying
		
		$document = new \DOMDocument("1.0", "UTF-8");
		$document->formatOutput = true;
		$document->preserveWhiteSpace = true;
		
		$html = mb_convert_encoding($data, "HTML-ENTITIES", "UTF-8");
		$document->loadHTML($html);
		
		return new \DOMXPath($document);
	}
	
	/**
	 * @throws JsonException
	 * @throws ParserException
	 */
	public function getJson(): array {
		$data = $this->downloadData();
		
		return Json::decode($data, Json::FORCE_ARRAY);
	}
	
	/**
	 * Gets movies and other data from the web page.
	 */
	abstract public function parse(): void;
}
