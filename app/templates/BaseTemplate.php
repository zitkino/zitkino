<?php

namespace App\Templates;

use App\Services\MetaService;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\SmartObject;
use Nette\Utils\Arrays;

class BaseTemplate extends Template {
	use SmartObject;
	
	/** @var MetaService */
	public $meta;
	
	/** @var string */
	public $locale;
	
	public function setParameters(array $params): BaseTemplate {
		return Arrays::toObject($params, $this);
	}
}
