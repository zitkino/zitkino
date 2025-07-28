<?php
namespace App\Logging;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class CinemaLoggerFactory {
	private string $baseDir;
	
	public function __construct(string $baseDir, private LoggerInterface $baseLogger) {
		$this->baseDir = $baseDir;
	}
	
	public function filename(string $filename): LoggerInterface {
		if($this->baseLogger instanceof Logger) {
			$dynamicFileHandler = new DynamicFileHandler($this->baseDir);
			$dynamicFileHandler->setFilename($filename);
			
			return $this->baseLogger->pushHandler($dynamicFileHandler);
		}
		
		return $this->baseLogger;
	}
	
	public function log(string $message, array $context = []): void {
//		$this->baseLogger->log($level, $message);
	}
}
