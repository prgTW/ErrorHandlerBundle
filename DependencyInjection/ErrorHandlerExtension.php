<?php

namespace prgTW\ErrorHandlerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ErrorHandlerExtension extends Extension
{
	/** {@inheritdoc} */
	public function load(array $configs, ContainerBuilder $container)
	{
		$configuration = new Configuration();
		$config        = $this->processConfiguration($configuration, $configs);

		$loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
		$loader->load('error-handler-services.xml');

		if (!$config['shutdown_listener']['enabled'])
		{
			$container->removeDefinition('error_handler.listener.shutdown');
		}
		if (!$config['exception_listener']['enabled'])
		{
			$container->removeDefinition('error_handler.listener.exception');
		}
		if (!$config['terminate_listener']['enabled'])
		{
			$container->removeDefinition('error_handler.listener.terminate');
		}

		$container->setParameter('error_handler.stage', $config['stage']);
		$container->setParameter('error_handler.root_dir', $config['root_dir']);

		$errorHandler = $container->getDefinition('error_handler');

		foreach ($config['categories'] as $categoryName => $categoryConfiguration)
		{
			if (empty($categoryConfiguration['handlers']))
			{
				continue;
			}
			foreach ($categoryConfiguration['handlers'] as $handlerName => $handlerConfiguration)
			{
				$handlerClass      = $container->getParameter(sprintf('error_handler.handler_%s.class', $handlerName));
				$handlerId         = sprintf('error_handler.handler.%s.%s', $categoryName, $handlerName);
				$handlerDefinition = new DefinitionDecorator('error_handler.abstract.handler');
				$handlerDefinition->setClass($handlerClass);
				$handlerDefinition->setPublic(false);
				$handlerDefinition->setLazy(true);
				switch ($handlerName)
				{
					case 'bugsnag':
						$handlerDefinition->addArgument($handlerConfiguration['apiKey']);
						if (isset($handlerConfiguration['endpoint']))
						{
							$handlerDefinition->addMethodCall('setEndpoint', array(
								$handlerConfiguration['endpoint']
							));
						}
						if (isset($handlerConfiguration['useSSL']))
						{
							$handlerDefinition->addMethodCall('setUseSSL', array(
								$handlerConfiguration['useSSL']
							));
						}
						break;
				}
				$container->setDefinition($handlerId, $handlerDefinition);
				$errorHandler->addMethodCall('addHandler', array(
					new Reference($handlerId),
					$categoryName == 'default' ? array() : array($categoryName)
				));
			}
		}
	}
}
