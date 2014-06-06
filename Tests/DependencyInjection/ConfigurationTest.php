<?php

namespace prgTW\ErrorHandlerBundle\Tests\DependencyInjection;

use prgTW\ErrorHandler\Error\Severity;
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
			'error_handler' => array(),
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

	/**
	 * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
	 */
	public function testInvalidSeverityConfigurations()
	{
		$this->extension->load(array(
			'error_handler' => array(
				'shutdown_severity' => 'unknown',
				'categories' => array(
					'default' => array(),
				),
			),
		), $this->container);
	}

	/**
	 * @dataProvider provideValidSeverityConfigurations
	 */
	public function testValidSeverityConfigurations($severity)
	{
		$this->extension->load(array(
			'error_handler' => array(
				'shutdown_severity' => $severity,
				'categories' => array(
					'default' => array(),
				),
			),
		), $this->container);

		$shutdownSeverity = strtoupper($severity);
		$this->assertEquals($shutdownSeverity, $this->container->getParameter('error_handler.shutdown_severity'));
		$baseErrorHandler = $this->container->getDefinition('base_error_handler');
		$this->assertEquals(Severity::$SEVERITIES[$shutdownSeverity], $baseErrorHandler->getArgument(0));
	}

	public function provideValidSeverityConfigurations()
	{
		return array(
			array('debug'),
			array('DEBUG'),
			array('info'),
			array('INFO'),
			array('notice'),
			array('NOTICE'),
			array('warning'),
			array('WARNING'),
			array('error'),
			array('ERROR'),
			array('critical'),
			array('CRITICAL'),
			array('alert'),
			array('ALERT'),
			array('emergency'),
			array('EMERGENCY'),
		);
	}

	/**
	 * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
	 * @dataProvider provideInvalidHandlersConfigurations
	 */
	public function testInvalidHandlersConfigurations($handlers)
	{
		$this->extension->load(array(
			'error_handler' => array(
				'categories' => array(
					'default' => array(
						'handlers' => $handlers,
					),
				),
			),
		), $this->container);
	}

	public function provideInvalidHandlersConfigurations()
	{
		return array(
			array(false),
			array(true),
			array(null),
			array(array()),
			array(array()),
			array(
				array(
					'bugsnag' => array()
				)
			),
			array(
				array(
					'raven' => array()
				)
			),
		);
	}

	/**
	 * @dataProvider provideValidHandlersConfiguration
	 */
	public function testHandlersConnected(array $handlers)
	{
		$this->extension->load(array(
			'error_handler' => array(
				'categories' => array(
					'default' => array(
						'handlers' => $handlers
					),
				),
			),
		), $this->container);

		foreach (array_keys($handlers) as $handlerName)
		{
			$serviceId = 'error_handler.handler.default.' . $handlerName;
			$this->assertTrue($this->container->hasDefinition($serviceId));
			$def = $this->container->getDefinition($serviceId);
			$this->assertFalse($def->isPublic());
			$this->assertTrue($def->isLazy());
		}
	}

	public function provideValidHandlersConfiguration()
	{
		return array(
			array(
				array(
					'bugsnag' => array(
						'apiKey'   => 'example',
						'endpoint' => 'endpoint',
						'useSSL'   => false,
					),
				),
			),
			array(
				array(
					'raven' => array(
						'endpoint' => 'endpoint',
					),
				),
			),
		);
	}

	protected function setUp()
	{
		$this->extension = new ErrorHandlerExtension();
		$this->container = new ContainerBuilder();
		$this->container->registerExtension($this->extension);
	}
}
