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

		/** @noinspection PhpUndefinedMethodInspection */
		$rootNode
			->fixXmlConfig('category', 'categories')
			->children()
				->scalarNode('stage')
					->defaultValue('%kernel.environment%')
				->end()

				->scalarNode('root_dir')
					->defaultValue('%kernel.root_dir%')
				->end()

				->arrayNode('categories')
					->useAttributeAsKey('categoryName')
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
		$root        = $treeBuilder->root('handlers');
		$root->treatFalseLike(array());
		$root->treatTrueLike(array());
		$root->treatNullLike(array());
		$root->validate()->ifTrue(function ($val) {
			return !is_array($val) || !count($val);
		})->thenInvalid('Define at least 1 handler');

		$this->appendBugsnagConfiguration($root);
		$this->appendRavenConfiguration($root);

		return $root;
	}

	/**
	 * @param ArrayNodeDefinition $root
	 */
	protected function appendBugsnagConfiguration(ArrayNodeDefinition $root)
	{
		$node =  $root->children()->arrayNode('bugsnag');
		$node->children()->scalarNode('apiKey')->isRequired()->info('Project API key');
		$node->children()->scalarNode('endpoint')->defaultNull()->info('Endpoint URI');
		$node->children()->booleanNode('useSSL')->defaultNull()->info('Whether to use SSL');
	}

	/**
	 * @param ArrayNodeDefinition $root
	 */
	protected function appendRavenConfiguration(ArrayNodeDefinition $root)
	{
		$node =  $root->children()->arrayNode('raven');
		$node->children()->scalarNode('endpoint')->isRequired()->defaultNull()->info('Endpoint URI');
	}
}
