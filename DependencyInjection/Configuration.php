<?php

namespace prgTW\ErrorHandlerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
	/** {@inheritdoc} */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode    = $treeBuilder->root('error_handler');

		$rootNode
			->fixXmlConfig('project')
			->children()
				->scalarNode('stage')
					->defaultValue('%kernel.environment%')
				->end()

				->scalarNode('root_dir')
					->defaultValue('%kernel.root_dir%')
				->end()

				->arrayNode('exception_listener')
					->canBeDisabled()
				->end()

				->arrayNode('shutdown_listener')
					->canBeDisabled()
				->end()

				->arrayNode('terminate_listener')
					->canBeDisabled()
				->end()

				->arrayNode('projects')
					->useAttributeAsKey('projectName')
					->validate()
					->ifTrue(function (array $v) {
						return !array_key_exists('default', $v);
					})
					->thenInvalid('"default" key has to be specified')
					->end()
					->prototype('array')
						->children()
							->append($this->getHandlerNodes())
						->end()
					->end()
				->end()
			->end()
		;

		return $treeBuilder;
	}

	protected function getHandlerNodes()
	{
		$treeBuilder = new TreeBuilder();
		$root = $treeBuilder->root('handlers');

		$this->appendBugsnagConfiguration($root);

		return $root;
	}

	/**
	 * @param ArrayNodeDefinition $node
	 */
	protected function appendBugsnagConfiguration(ArrayNodeDefinition $node)
	{
		$bugsnag =  $node->children()->arrayNode('bugsnag');
		$bugsnag->isRequired();
		$bugsnag->children()->scalarNode('apiKey')->isRequired()->info('Project API key');
		$bugsnag->children()->scalarNode('endpoint')->defaultNull()->info('Endpoint URI');
		$bugsnag->children()->booleanNode('useSSL')->defaultNull()->info('Whether to use SSL');
	}
}
