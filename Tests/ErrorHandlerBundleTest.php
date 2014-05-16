<?php

namespace prgTW\ErrorHandlerBundle\Tests;

use prgTW\ErrorHandlerBundle\DependencyInjection\Compiler\ProcessorsPass;
use prgTW\ErrorHandlerBundle\ErrorHandlerBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ErrorHandlerBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessorPass()
    {
        $container = new ContainerBuilder();
        $ErrorHandlerBundle = new ErrorHandlerBundle();

        $ErrorHandlerBundle->build($container);

        $compilerPasses = $container->getCompilerPassConfig()->getPasses();
		$processorPasses = array_filter($compilerPasses, function (CompilerPassInterface $compilerPass) {
			return $compilerPass instanceof ProcessorsPass;
		});

        $this->assertCount(1, $processorPasses);
    }
}
