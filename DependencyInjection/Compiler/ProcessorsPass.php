<?php

namespace prgTW\ErrorHandlerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ProcessorsPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasDefinition('error_handler'))
		{
			// @codeCoverageIgnoreStart
			return;
			// @codeCoverageIgnoreEnd
		}

		$interface      = 'prgTW\\ErrorHandler\\Processor\\ProcessorInterface';
		$errorHandler   = $container->getDefinition('error_handler');
		$taggedServices = $container->findTaggedServiceIds('error_handler.processor');

		foreach (array_keys($taggedServices) as $serviceId)
		{
			$processorService = $container->getDefinition($serviceId);
			$class            = $container->getParameterBag()->resolveValue($processorService->getClass());
			$ref              = new \ReflectionClass($class);
			if (!$ref->implementsInterface($interface))
			{
				throw new \LogicException(sprintf('"%s" must implement "%s"', $class, $interface));
			}

			$errorHandler->addMethodCall('addProcessor', array(
				new Reference($serviceId),
			));
		}
	}

}
