<?php
use Nette\Application\Application;
use Nette\Configurator;

// Let bootstrap create Dependency Injection container.
$configurator = new Configurator;

if(isset($_ENV["APP_ENV"])) {
	switch($_ENV["APP_ENV"]) {
		case "development": default:
			$configurator->setDebugMode(true);
			break;
			
		case "stage":
		case "production":
			$configurator->setDebugMode(false);
			break;
	}
} else {
	throw new RuntimeException("You did not set environment in .env file!");
}

// Enable Nette Debugger for error visualisation & logging
$logDir = __DIR__."/../_log";
if(!file_exists($logDir)) { mkdir($logDir, 0777, true); }
$configurator->enableTracy($logDir);

$tempDir = __DIR__."/../.temp";
if(!file_exists($tempDir)) { mkdir($tempDir, 0777, true); }
$configurator->setTempDirectory($tempDir);

// Enable RobotLoader - this will load all classes automatically
$configurator->createRobotLoader()->addDirectory(__DIR__)->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__."/config/config.neon");
$configurator->addConfig(__DIR__."/config/services.neon");
$configurator->addConfig(__DIR__."/config/keys.neon");
$configurator->addConfig(__DIR__."/config/".$_ENV["APP_ENV"].".neon");

$container = $configurator->createContainer();

// Run application.
$container->getByType(Application::class)->run();
