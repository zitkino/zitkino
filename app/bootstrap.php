<?php
use Nette\Application\Application;

// Let bootstrap create Dependency Injection container.
$configurator = new Nette\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setDebugMode(true);
$logDir = __DIR__."/../_log";
if(!file_exists($logDir)) { mkdir($logDir, 0777, true); }
$configurator->enableTracy($logDir);

// Enable RobotLoader - this will load all classes automatically
$tempDir = __DIR__."/../.temp";
if(!file_exists($tempDir)) { mkdir($tempDir, 0777, true); }
$configurator->setTempDirectory($tempDir);

$configurator->createRobotLoader()->addDirectory(__DIR__)->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__."/config/config.neon");
$configurator->addConfig(__DIR__."/config/services.neon");

switch($_ENV["APP_ENV"]) {
	case "dev":
	case "development":
	default:
		$configurator->addConfig(__DIR__."/config/development.neon");
		break;
	case "prod":
	case "production":
		$configurator->addConfig(__DIR__."/config/production.neon");
		break;
}

$container = $configurator->createContainer();

// Run application.
$container->getByType(Application::class)->run();
