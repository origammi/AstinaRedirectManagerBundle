<?php

namespace Astina\Bundle\RedirectManagerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AstinaRedirectManagerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $loader        = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yml');

        // if redirect_subdomain is present, than we set up listener
        if (isset($config['redirect_subdomains'])) {
            $routerDefinition    = $container->getDefinition('armb.router');
            $routerDefinitionTag = $routerDefinition->getTag('kernel.event_listener');
            $routerDefinitionTag = reset($routerDefinitionTag);

            $subDomainDefinition = $container->getDefinition('armb.subdomain');
            $subDomainDefinition->addTag(
                'kernel.event_listener',
                array(
                    'event'    => 'kernel.request',
                    'method'   => 'onKernelRequest',
                    'priority' =>  $routerDefinitionTag['priority'] - 1 //has to be lower than armb_router listener
                )
            );

            // let's add customizable arguments
            $subDomainDefinition->addArgument($config['redirect_subdomains']['route_name']);
            $subDomainDefinition->addArgument($config['redirect_subdomains']['route_params']);
            $subDomainDefinition->addArgument($config['redirect_subdomains']['redirect_code']);
        }
    }
}
