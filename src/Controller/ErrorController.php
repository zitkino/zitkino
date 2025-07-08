<?php

namespace App\Controller;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

/**
 * Error controller.
 */
class ErrorController extends AbstractController
{
	/** @var LoggerInterface */
	private $logger;

	public function __construct(
		LoggerInterface $logger,
		\Symfony\Contracts\Translation\TranslatorInterface $translator,
		\Symfony\Component\HttpFoundation\RequestStack $requestStack,
		\App\Service\MetaService $metaService,
		\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag
	) {
		parent::__construct($translator, $requestStack, $metaService, $parameterBag);
		$this->logger = $logger;
	}

	/**
	 * This method is used by the error_controller service to render custom error pages.
	 * It's configured in config/services.yaml.
	 */
	public function show(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null): Response
	{
		// Log the error
		$this->logger->error(sprintf(
			'Error %s: %s in %s line %s',
			$exception->getStatusCode(),
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine()
		));

		// For AJAX requests, return a JSON response
		if ($request->isXmlHttpRequest()) {
			return $this->json(['error' => true], $exception->getStatusCode());
		}

		// Determine the template based on the status code
		$template = $this->getErrorTemplate($exception->getStatusCode());

		// Render the template
		return $this->render($template, [
			'status_code' => $exception->getStatusCode(),
			'status_text' => Response::$statusTexts[$exception->getStatusCode()] ?? 'Unknown Error',
			'exception' => $exception,
		], new Response('', $exception->getStatusCode()));
	}

	/**
	 * Get the appropriate error template based on the status code.
	 */
	private function getErrorTemplate(int $statusCode): string
	{
		if (in_array($statusCode, [403, 404, 405, 410, 500])) {
			return sprintf('error/%d.html.twig', $statusCode);
		}

		if ($statusCode >= 400 && $statusCode < 500) {
			return 'error/4xx.html.twig';
		}

		return 'error/500.html.twig';
	}
}
