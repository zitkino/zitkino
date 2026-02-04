<?php

namespace App\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class DatabaseRoute {
	public function __construct(public ?array $path = null, public string $name = "", public string $entityClass = "") {
	}
}
