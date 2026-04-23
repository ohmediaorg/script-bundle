<?php

namespace OHMedia\ScriptBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class OHMediaScriptBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('iframe_src_prefixes')->end()
                ->arrayNode('script_src_prefixes')->end()
            ->end()
        ;
    }

    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder,
    ): void {
        $containerConfigurator->import('../config/services.yaml');

        $containerConfigurator->parameters()
            ->set('oh_media_script.iframe_src_prefixes', $config['iframe_src_prefixes'])
            ->set('oh_media_script.script_src_prefixes', $config['script_src_prefixes'])
        ;
    }
}
