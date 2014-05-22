<?php

namespace prgTW\ErrorHandlerBundle\Processor;

use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandler\Processor\ProcessorInterface;

class EnvironmentProcessor implements ProcessorInterface
{
	/** {@inheritdoc} */
	public function process(Metadata $metadata, \Exception $exception = null)
	{
		$metadata->addTag('php_version', phpversion());
		$metadata->addMetadatum('php_version', phpversion());

		$metadata->addTag('php_sapi_name', php_sapi_name());
		$metadata->addMetadatum('php_sapi_name', php_sapi_name());

		$metadata->addTag('hostname', gethostname());
		$metadata->addMetadatum('hostname', gethostname());
	}

}
