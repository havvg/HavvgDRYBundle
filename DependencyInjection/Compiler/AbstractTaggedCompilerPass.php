<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractTaggedCompilerPass implements CompilerPassInterface
{
    protected $tag;
    protected $targetService;
    protected $targetMethod;

    public function process(ContainerBuilder $container)
    {
        if (null === $this->targetService) {
            return;
        }

        if (!$container->hasDefinition($this->targetService)) {
            return;
        }

        foreach ($container->findTaggedServiceIds($this->tag) as $id => $tags) {
            $container
                ->getDefinition($this->targetService)
                ->addMethodCall($this->targetMethod, array(new Reference($id)))
            ;
        }
    }
}
