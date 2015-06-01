<?php

namespace Havvg\Bundle\DRYBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AddGlobalObjectsCompilerPass;

final class HavvgDRYBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddGlobalObjectsCompilerPass());
    }
}
