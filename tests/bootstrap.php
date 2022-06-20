<?php

use App\Bootstrap;
use Tester\Environment;

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../app/Bootstrap.php";

$temp = __DIR__."/../.temp";
if(!is_dir($temp)) {
	mkdir($temp, 0777, true);
}

Environment::setup();
date_default_timezone_set("Europe/Prague");

return Bootstrap::boot()->createContainer();
