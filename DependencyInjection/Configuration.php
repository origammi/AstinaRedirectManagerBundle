<?php

namespace Astina\Bundle\RedirectManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('astina_redirect_manager');

        $rootNode
            ->children()
            ->booleanNode('enable_listeners')
                ->defaultValue(true)
            ->end()
            ->scalarNode('base_layout')
                ->defaultValue(null)
            ->end()
            ->arrayNode('storage')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('entity_manager')
                        ->defaultValue(null)
                    ->end()
                ->end()
            ->end()
            ->arrayNode('redirect_subdomains')
                ->children()
                    ->scalarNode('enabled')
                        ->defaultTrue()
                    ->end()
                    ->scalarNode('route_name')
                        ->isRequired()
                    ->end()
                    ->arrayNode('route_params')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                    ->integerNode('redirect_code')
                        ->defaultValue(301)
                        ->min(300)
                        ->max(308)
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
