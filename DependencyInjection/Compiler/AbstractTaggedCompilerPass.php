<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractTaggedCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $targetService;

    /**
     * @var string
     */
    protected $targetMethod;

    /**
     * References a target services with tagged services.
     *
     * The target method will be called on the target service for all services with a given tag.
     * The argument list defaults to the tagged service.
     *
     * @see AbstractTaggedCompilerPass::getTargetService
     * @see AbstractTaggedCompilerPass::getTargetMethod
     * @see AbstractTaggedCompilerPass::getTag
     * @see AbstractTaggedCompilerPass::getArguments
     *
     * @param ContainerBuilder $container
     */
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
     * Returns the argument list on the target method for a single service.
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

    /**
     * Returns the tag name of the services to find.
     *
     * @return string
     */
    protected function getTag()
    {
        return $this->tag;
    }

    /**
     * Returns the name of the method to be called on the target service.
     *
     * @return string
     */
    protected function getTargetMethod()
    {
        return $this->targetMethod;
    }

    /**
     * Returns the name of the target service to attach tagged services to using the target method.
     *
     * @return string
     */
    protected function getTargetService()
    {
        return $this->targetService;
    }
}
