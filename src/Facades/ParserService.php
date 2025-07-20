<?php

namespace App\Facades;

use App\Entity\Cinema;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParserService
{
	/** @var HttpClientInterface */
	private $httpClient;
    
	/** @var CinemaFacade */
	private $cinemaFacade;
    
	/** @var LoggerInterface */
	private $logger;
    
	/** @var object|null */
	private $parser;
    
	public function __construct(
		HttpClientInterface $httpClient,
		CinemaFacade $cinemaFacade,
		LoggerInterface $logger
	) {
		$this->httpClient = $httpClient;
		$this->cinemaFacade = $cinemaFacade;
		$this->logger = $logger;
	}
    
	public function getParser()
	{
		return $this->parser;
	}
    
	public function initParser(Cinema $cinema): void
	{
		try {
			// This is a simplified version of the parser initialization
			// In a real implementation, you would need to create the actual parser classes
			$this->parser = new \stdClass(); // Placeholder for the actual parser
            
			// Log that we're initializing the parser for this cinema
			$this->logger->info(sprintf('Initializing parser for cinema: %s', $cinema->getCode()));
            
			// Update the cinema's parsed timestamp
			$cinema->setParsed(new \DateTime());
			// Save the cinema
			// In a real implementation, you would need to persist this change
		} catch (\Exception $e) {
			$this->logger->error(sprintf('Error initializing parser for cinema %s: %s', $cinema->getCode(), $e->getMessage()));
		}
	}
    
	public function getCinemaFacade(): CinemaFacade
	{
		return $this->cinemaFacade;
	}
}
