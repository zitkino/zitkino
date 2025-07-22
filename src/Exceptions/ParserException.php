<?php
namespace App\Exceptions;

class ParserException extends \Exception {
	private string $url;
	
	public function __construct(string $message = "", int $code = 0, \Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
	
	public function getUrl(): string {
		return $this->url;
	}
	
	public function setUrl(string $url): ParserException {
		$this->url = $url;
		return $this;
	}
}
