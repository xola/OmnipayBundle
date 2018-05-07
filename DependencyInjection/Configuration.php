<?php

namespace Xola\OmnipayBundle\DependencyInjection;

use Guzzle\Log\MessageFormatter;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
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
            ->arrayNode('log')->children()
                ->scalarNode('format')
                    ->defaultValue(MessageFormatter::DEBUG_FORMAT);

        return $treeBuilder;
    }
}