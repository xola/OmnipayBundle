<?php

namespace Xola\OmnipayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const DEBUG_FORMAT = '>>>>>>>>\n{request}\n<<<<<<<<\n{response}\n--------\n{curl_stderr}';

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('omnipay');
        $rootNode->children()
            ->arrayNode('log')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('format')
            ->defaultValue(Configuration::DEBUG_FORMAT);

        return $treeBuilder;
    }
}