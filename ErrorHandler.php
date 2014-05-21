<?php

namespace prgTW\ErrorHandlerBundle;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Handler\HandlerInterface;
use prgTW\ErrorHandler\Metadata\Metadata;

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
	}

	/** {@inheritdoc} */
	public function handleError(ErrorException $error, Metadata $metadata)
	{
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
	public function handleException(\Exception $exception, Metadata $metadata)
	{
		$this->errorHandler->handleException($exception, $metadata);
	}

}
