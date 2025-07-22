<?php

namespace App\Controllers;

use App\Services\MetaService;
use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Error controller.
 */
class ErrorController extends BaseController {
	/** @var LoggerInterface */
	private $logger;
	
	public function __construct(LoggerInterface $logger, TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		parent::__construct($translator, $requestStack, $metaService);
		$this->logger = $logger;
	}
	
	/**
	 * This method is used by the error_controller service to render custom error pages.
	 * It's configured in config/services.yaml.
	 */
	public function show(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null): Response {
		// Log the error
		$this->logger->error(sprintf('Error %s: %s in %s line %s', $exception->getStatusCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine()));
		
		// For AJAX requests, return a JSON response
		if($request->isXmlHttpRequest()) {
			return $this->json(['error' => true], $exception->getStatusCode());
		}
		
		// Determine the template based on the status code
		$template = $this->getErrorTemplate($exception->getStatusCode());
		
		// Render the template
		return $this->render($template, ['status_code' => $exception->getStatusCode(), 'status_text' => Response::$statusTexts[$exception->getStatusCode()] ?? 'Unknown Error', 'exception' => $exception,], new Response('', $exception->getStatusCode()));
	}
	
	/**
	 * Get the appropriate error template based on the status code.
	 */
	private function getErrorTemplate(int $statusCode): string {
		if(in_array($statusCode, [403, 404, 405, 410, 500])) {
			return sprintf('error/%d.html.twig', $statusCode);
		}
		
		if($statusCode >= 400 && $statusCode < 500) {
			return 'error/4xx.html.twig';
		}
		
		return 'error/500.html.twig';
	}
}
