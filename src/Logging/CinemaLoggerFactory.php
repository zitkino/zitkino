<?php
namespace App\Logging;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class CinemaLoggerFactory {
	public function __construct(private LoggerInterface $baseLogger, private DynamicFileHandler $fileHandler) { }
	
	public function createLogger(string $cinemaCode): LoggerInterface {
		$this->fileHandler->setFilename($cinemaCode);
		
		if($this->baseLogger instanceof Logger) {
			return $this->baseLogger->pushHandler($this->fileHandler);
		}
		
		return $this->baseLogger;
	}
}
