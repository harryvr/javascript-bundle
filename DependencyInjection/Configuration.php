<?php

/*
 * This file is part of the JavascriptBundle package.
 * 
 * © Enzo Innocenzi <enzo.inno@gmail.com> - 2019
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Hawezo\JavascriptBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author Enzo Innocenzi <enzo.inno@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('javascript');
        $rootNode = $treeBuilder->getRootNode();

        // Translation
        $rootNode
            ->children()
                ->arrayNode('translation')
                    ->addDefaultsIfNotSet()
                    ->ignoreExtraKeys()
                    ->children()
                        ->scalarNode('extract_path')
                            ->cannotBeEmpty()
                            ->info('The extract path of the message file.')
                            ->defaultValue('public/build/messages.js')
                        ->end()
                        ->arrayNode('locales')
                            ->info('The locales to be exported')
                                ->prototype('scalar')
                                    ->defaultValue([])
                                ->end()
                            ->end()
                        ->arrayNode('domains')
                            ->info('The domains to be exported')
                                ->prototype('scalar')
                                    ->defaultValue([])
                                ->end()
                            ->end()
                    ->end()
                ->end()
                ->arrayNode('routing')
                    ->addDefaultsIfNotSet()
                    ->ignoreExtraKeys()
                    ->children()
                        ->scalarNode('extract_path')
                            ->cannotBeEmpty()
                            ->info('The extract path of the message file.')
                            ->defaultValue('public/build/routes.js')
                        ->end()
                        ->arrayNode('routes')
                            ->info('Defines a list of routes to be exposed or hidden, depending on the whitelist argument.')
                            ->beforeNormalization()
                                ->ifTrue(function ($v) { return !is_array($v); })
                                ->then(function ($v) { return array($v); })
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('whitelist')
                            ->defaultFalse()
                            ->info('Defines wehther or not the routes parameter will be used as a whitelist.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
