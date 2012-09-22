<?php

namespace Havvg\Bundle\DRYBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AddGlobalObjectsCompilerPass;

class HavvgDRYBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddGlobalObjectsCompilerPass());
    }
}
