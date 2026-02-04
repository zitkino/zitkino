<?php

namespace App\Services;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class Debugger {
	public static function dump(mixed ...$vars): void {
		(new HtmlDumper())->dump((new VarCloner())->cloneVar($vars));
	}
}
