<?php

/*
 * This file is part of the JavascriptBundle package.
 *
 * © Enzo Innocenzi <enzo@innocenzi.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace SymfonyJavascript\JavascriptBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Enzo Innocenzi <enzo@innocenzi.dev>
 */
class JavascriptExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // $loader = new YamlFileLoader(
        //     $container,
        //     new FileLocator(__DIR__.'/../Resources/config')
        // );
        // $loader->load('services.yml');
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        // There is surely a better way to retrieve this automatically? Right?
        $configuration = $this->getConfiguration($configs, $container);
        $config        = $this->processConfiguration($configuration, $configs);
        $definition    = $container->getDefinition('javascript');

        // translation
        foreach (['translation', 'routing'] as $i => $module) {
            array_walk($config[$module], function ($value, $key) use ($module, $container, $definition, $i) {
                $container->setParameter(sprintf('javascript.%s.%s', $module, $key), $value);
                $definition->setArgument($i++, $value);
            });
        }

        // Translation Extractor
        $container
            ->getDefinition('javascript.translation.extractor')
            ->replaceArgument(1, $config['translation']['locales'])
            ->replaceArgument(2, $config['translation']['domains']);

        // Route Extractor
        $container
            ->getDefinition('javascript.routing.extractor')
            ->replaceArgument(2, $config['routing']['routes'])
            ->replaceArgument(3, $config['routing']['whitelist']);
    }

    public function getConfiguration(array $configs, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }

    public function getAlias()
    {
        return 'javascript';
    }
}
