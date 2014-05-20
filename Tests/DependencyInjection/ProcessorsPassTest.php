<?php

namespace prgTW\ErrorHandlerBundle\Tests\DependencyInjection;

use prgTW\ErrorHandlerBundle\DependencyInjection\Compiler\ProcessorsPass;
use prgTW\ErrorHandlerBundle\DependencyInjection\ErrorHandlerExtension;
use prgTW\ErrorHandlerBundle\ErrorHandlerBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ProcessorsPassTest extends \PHPUnit_Framework_TestCase
{
	/** @var ContainerBuilder */
	protected $container;

	/** @var  ErrorHandlerExtension */
	protected $extension;

	/** @var ErrorHandlerBundle */
	protected $bundle;

	public function testProcessorPass()
	{
		$compilerPasses  = $this->container->getCompilerPassConfig()->getPasses();
		$processorPasses = array_filter($compilerPasses, function (CompilerPassInterface $compilerPass)
		{
			return $compilerPass instanceof ProcessorsPass;
		});

		$this->assertCount(1, $processorPasses);
	}

	public function testProcessorsConnected()
	{
		$def = new Definition('error_handler.test.processor');
		$def->setClass(get_class(new TestProcessor()));
		$def->addTag('error_handler.processor');
		$this->container->setDefinition('error_handler.test.processor', $def);
		$this->container->compile();

		$errorHandler      = $this->container->getDefinition('error_handler');
		$addProcessorCalls = array_filter($errorHandler->getMethodCalls(), function (array $call)
		{
			return $call[0] = 'addProcessor';
		});
		$this->assertCount(1, $addProcessorCalls);
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testInvalidProcessorNotConnected()
	{
		$def = new Definition('error_handler.test.processor');
		$def->setClass(get_class(new InvalidProcessor()));
		$def->addTag('error_handler.processor');
		$this->container->setDefinition('error_handler.test.processor', $def);
		$this->container->compile();
	}

	/**
	 * @return array
	 */
	protected function setUp()
	{
		$this->container = new ContainerBuilder();
		$this->extension = new ErrorHandlerExtension();
		$this->bundle    = new ErrorHandlerBundle();

		$this->container->registerExtension($this->extension);
		$this->container->loadFromExtension($this->extension->getAlias());
		$this->bundle->build($this->container);
	}
}
