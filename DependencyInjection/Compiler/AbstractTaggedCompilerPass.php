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
        if (null === $this->getTargetService()) {
            return;
        }

        if (!$container->hasDefinition($this->getTargetService())) {
            return;
        }

        foreach ($container->findTaggedServiceIds($this->getTag()) as $id => $tags) {
            $container
                ->getDefinition($this->getTargetService())
                ->addMethodCall($this->getTargetMethod(), $this->getArguments($id, $container))
            ;
        }
    }

    /**
     * Return the argument list on the target method for a single service.
     *
     * @param string           $id
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function getArguments($id, ContainerBuilder $container)
    {
        return array(new Reference($id));
    }

    protected function getTag()
    {
        return $this->tag;
    }

    protected function getTargetMethod()
    {
        return $this->targetMethod;
    }

    protected function getTargetService()
    {
        return $this->targetService;
    }
}
