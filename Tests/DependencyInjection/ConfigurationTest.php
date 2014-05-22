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

	public function testListeners()
	{
		$this->extension->load(array(
			'error_handler' => array(
			),
		), $this->container);

		$this->assertTrue($this->container->hasDefinition('error_handler.listener.exception'));
	}

	/**
	 * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
	 */
	public function testNoCategoriesInConfiguration()
	{
		$this->extension->load(array(
			'error_handler' => array(
				'categories' => array(),
			),
		), $this->container);
	}

	public function testDefaultCategoryDefined()
	{
		$this->extension->load(array(
			'error_handler' => array(
				'categories' => array(
					'default' => array(),
				),
			),
		), $this->container);
	}

	public function testHandlersConnected()
	{
		$this->extension->load(array(
			'error_handler' => array(
				'categories' => array(
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
