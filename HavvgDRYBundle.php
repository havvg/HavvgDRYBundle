<?php

namespace Havvg\Bundle\DRYBundle;

use Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AddGlobalObjectsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class HavvgDRYBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddGlobalObjectsPass());
    }
}
