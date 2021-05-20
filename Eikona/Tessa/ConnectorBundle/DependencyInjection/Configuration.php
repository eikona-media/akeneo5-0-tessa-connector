<?php

namespace Eikona\Tessa\ConnectorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link
 * http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $root = $treeBuilder->root('pim_eikona_tessa_connector');

        $children = $root->children();

        $children->arrayNode('settings')
            ->children()
            # base_url
            ->arrayNode('base_url')
            ->children()
            ->scalarNode('value')->end()
            ->scalarNode('scope')->end()
            ->end()
            ->end()
            # ui_url
            ->arrayNode('ui_url')
            ->children()
            ->scalarNode('value')->end()
            ->scalarNode('scope')->end()
            ->end()
            ->end()
            # username
            ->arrayNode('username')
            ->children()
            ->scalarNode('value')->end()
            ->scalarNode('scope')->end()
            ->end()
            ->end()
            # api_key
            ->arrayNode('api_key')
            ->children()
            ->scalarNode('value')->end()
            ->scalarNode('scope')->end()
            ->end()
            ->end()
            # system_identifier
            ->arrayNode('system_identifier')
            ->children()
            ->scalarNode('value')->end()
            ->scalarNode('scope')->end()
            ->end()
            ->end()
            # sync_in_background
            ->arrayNode('sync_in_background')
            ->children()
            ->booleanNode('value')->end()
            ->scalarNode('scope')->end()
            ->end()
            ->end()
            # chunk_size
            ->arrayNode('chunk_size')
            ->children()
            ->integerNode('value')->min(1)->defaultValue(100)->end()
            ->scalarNode('scope')->end()
            ->end()
            ->end()
            # user_used_by_tessa
            ->arrayNode('user_used_by_tessa')
            ->children()
            ->scalarNode('value')->end()
            ->scalarNode('scope')->end()
            ->end()
            ->end()
            # disable_asset_editing_in_akeneo_ui
            ->arrayNode('disable_asset_editing_in_akeneo_ui')
            ->children()
            ->booleanNode('value')->end()
            ->scalarNode('scope')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        $children->end();

        $root->end();

        return $treeBuilder;
    }
}
