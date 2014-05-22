<?php

namespace prgTW\ErrorHandlerBundle\Processor;

use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandler\Processor\ProcessorInterface;

class EnvironmentProcessor implements ProcessorInterface
{
	/** {@inheritdoc} */
	public function process(Metadata $metadata, \Exception $exception = null)
	{
		$metadata->setTag('php_version', phpversion());
		$metadata->setMetadatum('php_version', phpversion());

		$metadata->setTag('php_sapi_name', php_sapi_name());
		$metadata->setMetadatum('php_sapi_name', php_sapi_name());

		$metadata->setTag('hostname', gethostname());
		$metadata->setMetadatum('hostname', gethostname());
	}

}
