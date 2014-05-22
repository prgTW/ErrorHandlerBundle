<?php

namespace prgTW\ErrorHandlerBundle\Listener;

use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandlerBundle\ErrorHandler;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionListener
{
	/** @var \prgTW\ErrorHandlerBundle\ErrorHandler */
	protected $errorHandler;

	/**
	 * @param ErrorHandler $errorHandler
	 */
	public function __construct(ErrorHandler $errorHandler)
	{
		$this->errorHandler = $errorHandler;
	}

	/**
	 * @param GetResponseForExceptionEvent $event
	 */
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		$exception = $event->getException();
		if ($exception instanceof HttpException)
		{
			return;
		}

		$culprit = null;
		if ($event->getRequest()->attributes->has('_controller'))
		{
			$culprit = $event->getRequest()->attributes->get('_controller');
		}

		$metadata = new Metadata();
		$metadata->addMetadatum('culprit', $culprit);
		$this->errorHandler->handleException($exception, $metadata);
	}

	/**
	 * @param ConsoleExceptionEvent $event
	 */
	public function onConsoleException(ConsoleExceptionEvent $event)
	{
		$exception = $event->getException();

		$metadata = new Metadata();
		$metadata->addMetadatum('commandName', $event->getInput()->getFirstArgument());
		$metadata->addMetadatum('command', (string)$event->getInput());
		$metadata->addMetadatum('exitCode', $event->getExitCode());
		$this->errorHandler->handleException($exception, $metadata);
	}
}
