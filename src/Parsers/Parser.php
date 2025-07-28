<?php
namespace App\Parsers;

use App\Entities\Cinema;
use App\Exceptions\ParserException;
use App\Services\ParserService;
use Doctrine\DBAL\Connection;
use Nette\Utils\{Json, JsonException};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

/**
 * Parser.
 */
abstract class Parser {
	protected Cinema $cinema;
	
	private ?string $url = null;
	
	protected Connection $connection;
	
	protected ParserService $parserService;
	
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
	protected function downloadData(): string {
		try {
			$response = $this->parserService->getHttpClient()
				->request(Request::METHOD_GET, $this->url);
			$body = $response->getContent();
		} catch(ExceptionInterface $e) {
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

//		$html = htmlspecialchars_decode(iconv("UTF-8", "ISO-8859-1", htmlentities($data, ENT_COMPAT, "UTF-8")), ENT_QUOTES);
		$document->loadHTML($data);
		
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
