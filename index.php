<?php
if(isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] === "https") {
	$_SERVER["HTTPS"] = "on";
	//$_SERVER["SERVER_PORT"] = "443";
}

// Uncomment this line if you must temporarily take down your site for maintenance.
// require ".maintenance.php";

// Add Nette
require_once __DIR__."/libs/autoload.php";

// Let bootstrap create Dependency Injection container.
$configurator = new Nette\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setDebugMode(true);
$logDir = __DIR__."/_log";
if(!file_exists($logDir)) { mkdir($logDir, 0777, true); }
$configurator->enableDebugger($logDir);

// Enable RobotLoader - this will load all classes automatically
$tempDir = __DIR__."/.temp";
if(!file_exists($tempDir)) { mkdir($tempDir, 0777, true); }
$configurator->setTempDirectory($tempDir);
$configurator->createRobotLoader()->addDirectory(__DIR__."/app")->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__."/app/config.neon");
//$configurator->addConfig(__DIR__."/app/config.local.neon"); // none section
$container = $configurator->createContainer();

// Run application.
$container->getService("application")->run();
return $container;
