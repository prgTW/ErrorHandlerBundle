<?php

namespace prgTW\ErrorHandlerBundle;

use prgTW\ErrorHandlerBundle\DependencyInjection\Compiler\ProcessorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ErrorHandlerBundle extends Bundle
{
	/** {@inheritdoc} */
	public function build(ContainerBuilder $container)
	{
		parent::build($container);

		$container->addCompilerPass(new ProcessorsPass());
	}
}
