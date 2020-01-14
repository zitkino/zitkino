<?php
namespace App;

use Nette\Configurator;

class Bootstrap {
	public static function boot(): Configurator {
		// Let bootstrap create Dependency Injection container.
		$configurator = new Configurator;
		$configurator->setTimeZone("Europe/Prague");
		
		// Enable Tracy for error visualization
		if(isset($_ENV["APP_ENV"])) {
			switch($_ENV["APP_ENV"]) {
				case "development":
				default:
					$configurator->setDebugMode(true);
					break;
				
				case "stage":
				case "production":
					$configurator->setDebugMode(false);
					break;
			}
		} else {
			throw new \RuntimeException("You did not set environment in .env file!");
		}
		
		// Enable error logging
		$logDir = __DIR__."/../_log";
		if(!is_dir($logDir)) {
			mkdir($logDir, 0777, true);
		}
		$configurator->enableTracy($logDir);
		
		$tempDir = __DIR__."/../.temp";
		if(!is_dir($tempDir)) {
			mkdir($tempDir, 0777, true);
		}
		$configurator->setTempDirectory($tempDir);
		
		// Enable RobotLoader - this will load all classes automatically
		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();
		
		$configurator->addConfig(__DIR__."/config/config.neon");
		$configurator->addConfig(__DIR__."/config/extensions.neon");
		$configurator->addConfig(__DIR__."/config/services.neon");
		$configurator->addConfig(__DIR__."/config/keys.neon");
		$configurator->addConfig(__DIR__."/config/parameters.neon");
		$configurator->addConfig(__DIR__."/config/".$_ENV["APP_ENV"].".neon");
		
		return $configurator;
	}
}

