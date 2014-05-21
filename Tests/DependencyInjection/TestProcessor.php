<?php

namespace prgTW\ErrorHandlerBundle\Tests\DependencyInjection;

use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandler\Processor\ProcessorInterface;

class TestProcessor implements ProcessorInterface
{
	/** {@inheritdoc} */
	public function process(Metadata $metadata, \Exception $exception = null)
	{

	}

}
