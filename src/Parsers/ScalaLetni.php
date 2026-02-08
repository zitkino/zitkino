<?php
namespace App\Parsers;

use App\Exceptions\ParserException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

/**
 * Scalní letňák parser.
 */
class ScalaLetni extends Scala {
	protected function downloadData(): string {
		try {
			$parameters = ["cinema" => ["5"], "hall" => [["26"], ["27"]], "_locale" => "cs"];
			$response = $this->parserService->httpClient->request(Request::METHOD_POST, $this->getUrl(), ["body" => $parameters]);
			$body = $response->getContent();
		} catch(ExceptionInterface $e) {
			$e = new ParserException($e->getMessage());
			$e->setUrl($this->getUrl());
			throw $e;
		}
		
		return $body ?: "";
	}
}
