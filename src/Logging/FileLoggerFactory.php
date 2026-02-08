<?php
namespace App\Logging;

use Monolog\Formatter\JsonFormatter;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class FileLoggerFactory {
	private string $baseDir;
	
	public function __construct(string $baseDir, private LoggerInterface $logger) {
		$this->baseDir = $baseDir;
	}
	
	public function filename(string $filename): LoggerInterface {
		if($this->logger instanceof Logger) {
			$fileHandler = new FileHandler($this->baseDir);
			
			if(str_ends_with($filename, ".json")) {
				$fileHandler->setFormatter(new JsonFormatter());
				$fileHandler->fileExtension = ".json";
				$filename = str_replace(".json", "", $filename);
			}
			
			$fileHandler->setFilename($filename);
			
			$this->logger->pushHandler($fileHandler);
		}
		
		return $this->logger;
	}
	
	public function log(string $message, array $context = [], string|int|Level $level = Level::Info): void {
		$this->logger->log($level, $message, $context);
	}
	
	public function withName(string $name): Logger|LoggerInterface {
		if($this->logger instanceof Logger) {
			$this->logger = $this->logger->withName($name);
		}
		
		return $this->logger;
	}
}
