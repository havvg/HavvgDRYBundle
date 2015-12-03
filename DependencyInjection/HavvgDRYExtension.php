<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection;

use Havvg\Bundle\DRYBundle\DependencyInjection\Loader\ServicesLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class HavvgDRYExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $servicesLoader = new ServicesLoader(__DIR__.'/../Resources/config/services', $container);
        $servicesLoader->load();
    }
}
