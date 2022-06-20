<?php
namespace Zitkino\Exceptions;

use Throwable;

class ParserException extends \Exception {
	/** @var string */
	private $url;
	
	public function getUrl(): string {
		return $this->url;
	}
	
	public function setUrl(string $url): ParserException {
		$this->url = $url;
		return $this;
	}
	
	public function __construct(string $message = "", int $code = 0, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}
