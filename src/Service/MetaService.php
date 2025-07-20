<?php

namespace App\Service;

class MetaService {
	private array $parameters;
	
	public function __construct(array $parameters = []) {
		$this->parameters = $parameters;
	}
	
	/**
	 * @return array|string|null
	 */
	public function get(string $key) {
		if(array_key_exists($key, $this->parameters)) {
			return $this->parameters[$key];
		}
		return null;
	}
}
