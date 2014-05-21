<?php

namespace prgTW\ErrorHandlerBundle\Processor;

use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandler\Processor\ProcessorInterface;
use Symfony\Component\HttpKernel\Kernel;

class BasicDataProcessor implements ProcessorInterface
{
	/** @var string */
	protected $environment;

	/** @var string */
	protected $kernelRootDir;

	/**
	 * @param string $environment
	 * @param string $kernelRootDir
	 */
	public function __construct($environment, $kernelRootDir)
	{
		$this->environment   = $environment;
		$this->kernelRootDir = $kernelRootDir;
	}

	/** {@inheritdoc} */
	public function process(Metadata $metadata, \Exception $exception = null)
	{
		$metadata->setStage($this->environment);
		$metadata->setTag('symfony_version', Kernel::VERSION);
		$metadata->setMetadatum('symfony_version', Kernel::VERSION);
		$metadata->setAppRootDir($this->kernelRootDir);
	}

}
