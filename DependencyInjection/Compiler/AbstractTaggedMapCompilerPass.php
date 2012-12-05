<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractTaggedMapCompilerPass implements CompilerPassInterface
{
    protected $mapServiceTag;
    protected $targetServiceTag;
    protected $targetMethod;

    public function process(ContainerBuilder $container)
    {
        $mapServices = array();
        foreach ($container->findTaggedServiceIds($this->getMapServiceTag()) as $id => $tags) {
            $targetId = &$tags[0]['target'];

            if (empty($mapServices[$targetId])) {
                $mapServices[$targetId] = array();
            }

            $mapServices[$targetId][] = $id;
        }

        if (empty($mapServices)) {
            return;
        }

        foreach ($container->findTaggedServiceIds($this->getTargetServiceTag()) as $id => $tags) {
            $alias = &$tags[0]['alias'];

            if (!empty($mapServices[$alias])) {
                $targetDefinition = $container->getDefinition($id);

                foreach ($mapServices[$alias] as $eachMapServiceId) {
                    $targetDefinition->addMethodCall($this->getTargetMethod(), $this->getArguments($eachMapServiceId, $container));
                }
            }
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

    protected function getMapServiceTag()
    {
        return $this->mapServiceTag;
    }

    protected function getTargetMethod()
    {
        return $this->targetMethod;
    }

    protected function getTargetServiceTag()
    {
        return $this->targetServiceTag;
    }
}
