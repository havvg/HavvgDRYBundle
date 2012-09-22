<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Havvg\Bundle\DRYBundle\DependencyInjection\Loader\ServicesLoader;

class HavvgDRYExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $servicesLoader = new ServicesLoader(__DIR__.'/../Resources/config/services', $container);
        $servicesLoader->load();
    }
}
