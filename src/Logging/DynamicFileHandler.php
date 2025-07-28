<?php

namespace App\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\LogRecord;

class DynamicFileHandler extends StreamHandler {
	private string $baseDir;
	
	private string $fileExtension;
	
	public function __construct(string $baseDir, string $fileExtension = '.log', int|string|Level $level = Level::Debug, bool $bubble = true) {
		$this->baseDir = rtrim($baseDir, '/');
		$this->fileExtension = $fileExtension;
		
		// Initialize with a temporary path that will be updated later
		parent::__construct('php://memory', $level, $bubble);
	}
	
	public function setFilename(string $filename): void {
		$path = sprintf('%s/%s%s', $this->baseDir, $filename, $this->fileExtension);
		
		// Close the existing stream if any
		$this->close();
		
		// Update the stream with the new path
		$this->url = $path;
		$this->stream = null; // Reset stream to force recreation with new path
	}
	
	protected function write(LogRecord $record): void {
		if($this->url === 'php://memory') {
			throw new \RuntimeException('Filename must be set using setFilename() before writing logs');
		}
		
		parent::write($record);
	}
}
