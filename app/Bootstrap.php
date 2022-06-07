<?php
declare(strict_types = 1);

namespace App;

use Dotenv\Dotenv;
use Nette\Configurator;

class Bootstrap {
	public static function boot(): Configurator {
		// Let bootstrap create Dependency Injection container.
		$configurator = new Configurator;
		$configurator->setTimeZone("Europe/Prague");
		
		$dotenv = Dotenv::createImmutable(__DIR__."/..");
		$dotenv->load();
		$dotenv->required("APP_ENV")->notEmpty();
		
		// Enable Tracy for error visualization
		if(isset($_ENV["APP_ENV"])) {
			switch($_ENV["APP_ENV"]) {
				case "development":
				default:
					$isDebug = true;
					break;
				case "stage":
				case "production":
					$isDebug = false;
					break;
			}
		} else {
			throw new \RuntimeException("You did not set environment in .env file!");
		}
		$configurator->setDebugMode($isDebug);
		
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
		
		define("WWW_DIR", __DIR__."/../");
		
		// Enable RobotLoader - this will load all classes automatically
		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();
		
		$configurator->addConfig(__DIR__."/config/config.neon");
		$configurator->addConfig(__DIR__."/config/env/".$_ENV["APP_ENV"].".neon");
		
		return $configurator;
	}
}
