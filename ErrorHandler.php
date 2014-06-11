<?php

namespace prgTW\ErrorHandlerBundle;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Error\Severity;
use prgTW\ErrorHandler\Handler\HandlerInterface;
use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandler\Processor\ProcessorInterface;

class ErrorHandler implements HandlerInterface
{
	/** @var \prgTW\ErrorHandler\ErrorHandler */
	protected $errorHandler;

	/**
	 * @param \prgTW\ErrorHandler\ErrorHandler $errorHandler
	 */
	public function __construct(\prgTW\ErrorHandler\ErrorHandler $errorHandler)
	{
		$this->errorHandler = $errorHandler;
		register_shutdown_function(array($this, 'handleShutdown'));
	}

	/** {@inheritdoc} */
	public function handleError(ErrorException $error, Metadata $metadata = null)
	{
		$this->addDefaultCategory($metadata);
		$this->errorHandler->handleError(
			$error->getCode(),
			$error->getMessage(),
			$error->getFile(),
			$error->getLine(),
			$error->getContext(),
			$metadata
		);
	}

	/** {@inheritdoc} */
	public function handleException(\Exception $exception, Metadata $metadata = null)
	{
		$this->addDefaultCategory($metadata);
		$this->errorHandler->handleException($exception, $metadata);
	}

	/** {@inheritdoc} */
	public function handleEvent($event, Metadata $metadata = null)
	{
		$this->addDefaultCategory($metadata);
		$this->errorHandler->handleEvent($event, $metadata);
	}

	/**
	 * Handle fatal errors and such
	 *
	 * @codeCoverageIgnore
	 */
	public function handleShutdown()
	{
		$error = error_get_last();
		if ($error && Severity::fromPhpErrorNo($error['type']) >= $this->errorHandler->getMinSeverityOnShutdown())
		{
			$error = ErrorException::fromPhpError($error['type'], $error['message'], $error['file'], $error['line']);
			$this->handleError($error);
		}
	}

	/**
	 * @param HandlerInterface $handler
	 * @param array            $categories
	 */
	public function addHandler(HandlerInterface $handler, array $categories = array())
	{
		$categories = array() !== $categories ? $categories : array('default');
		$this->errorHandler->getHandlerManager()->attach($handler, $categories);
	}

	/**
	 * @param ProcessorInterface $processor
	 */
	public function addProcessor(ProcessorInterface $processor)
	{
		$this->errorHandler->getProcessorManager()->attach($processor);
	}

	/**
	 * @param Metadata $metadata
	 */
	protected function addDefaultCategory(Metadata $metadata)
	{
		if (array() === $metadata->getCategories())
		{
			$metadata->addCategory('default');
		}
	}

}
