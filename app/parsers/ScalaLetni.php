<?php
namespace Zitkino\Parsers;

use GuzzleHttp\Exception\GuzzleException;
use Zitkino\Exceptions\ParserException;

/**
 * Scalní letňák parser.
 */
class ScalaLetni extends Scala {
	protected function downloadData(): string {
		try {
			$parameters = ["cinema" => ["5"], "hall" => [["26"], ["27"]], "_locale" => "cs"];
			$response = $this->parserService->getClientFactory()->createClient()->post($this->getUrl(), ["form_params" => $parameters]);
			$body = (string)$response->getBody();
		} catch(GuzzleException $e) {
			$e = new ParserException($e->getMessage());
			$e->setUrl($this->getUrl());
			throw $e;
		}
		
		return $body ?: "";
	}
}
