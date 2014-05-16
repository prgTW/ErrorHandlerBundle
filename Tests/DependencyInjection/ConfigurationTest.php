<?php

namespace prgTW\ErrorHandlerBundle\Tests\DependencyInjection;

use prgTW\ErrorHandlerBundle\DependencyInjection\ErrorHandlerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers prgTW\ErrorHandlerBundle\DependencyInjection\Configuration
 * @covers prgTW\ErrorHandlerBundle\DependencyInjection\ErrorHandlerExtension
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
	/** @var ErrorHandlerExtension */
	protected $extension;

	/** @var ContainerBuilder */
	protected $container;

	/**
	 * @dataProvider getConfigForListeners
	 */
	public function testListeners($exceptionListener, $shutdownListener, $terminateListener)
	{
		$this->extension->load(array(
			'error_handler' => array(
				'exception_listener' => array(
					'enabled' => $exceptionListener,
				),
				'shutdown_listener'  => array(
					'enabled' => $shutdownListener,
				),
				'terminate_listener' => array(
					'enabled' => $terminateListener,
				),
			),
		), $this->container);

		$this->assertEquals($exceptionListener, $this->container->hasDefinition('error_handler.listener.exception'));
		$this->assertEquals($shutdownListener, $this->container->hasDefinition('error_handler.listener.shutdown'));
		$this->assertEquals($terminateListener, $this->container->hasDefinition('error_handler.listener.terminate'));
	}

	public function getConfigForListeners()
	{
		$ret = array();
		for ($i = 0; $i < 8; ++$i)
		{
			$ret[] = array((bool)($i & 4), (bool)($i & 2), (bool)($i & 1));
		}

		return $ret;
	}

	/**
	 * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
	 */
	public function testNoProjectsInConfiguration()
	{
		$this->extension->load(array(
			'error_handler' => array(
				'projects' => array(),
			),
		), $this->container);
	}

	public function testDefaultProjectDefined()
	{
		$this->extension->load(array(
			'error_handler' => array(
				'projects' => array(
					'default' => array(),
				),
			),
		), $this->container);
	}

	public function testHandlersConnected()
	{
		$this->extension->load(array(
			'error_handler' => array(
				'projects' => array(
					'default' => array(
						'handlers' => array(
							'bugsnag' => array(
								'apiKey' => 'example',
								'endpoint' => 'endpoint',
								'useSSL' => false,
							),
						),
					),
				),
			),
		), $this->container);

		$serviceId = 'error_handler.handler.default.bugsnag';
		$this->assertTrue($this->container->hasDefinition($serviceId));
		$def = $this->container->getDefinition($serviceId);
		$this->assertFalse($def->isPublic());
		$this->assertTrue($def->isLazy());
		$this->assertEquals('example', $def->getArgument(0));
		$this->assertCount(2, $def->getMethodCalls());
	}

	protected function setUp()
	{
		$this->extension = new ErrorHandlerExtension();
		$this->container = new ContainerBuilder();
		$this->container->registerExtension($this->extension);
	}
}
