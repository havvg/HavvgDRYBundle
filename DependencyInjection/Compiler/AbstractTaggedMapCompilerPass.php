<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AbstractTaggedMapCompilerPass implements CompilerPassInterface
{
    protected $mapServiceTag;
    protected $targetServiceTag;
    protected $targetMethod;

    public function process(ContainerBuilder $container)
    {
        $mapServices = array();
        foreach ($container->findTaggedServiceIds($this->mapServiceTag) as $id => $tags) {
            $targetId = &$tags[0]['target'];

            if (empty($mapServices[$targetId])) {
                $mapServices[$targetId] = array();
            }

            $mapServices[$targetId][] = $id;
        }

        if (empty($mapServices)) {
            return;
        }

        foreach ($container->findTaggedServiceIds($this->targetServiceTag) as $id => $tags) {
            $alias = $tags[0]['alias'];

            if (!empty($mapServices[$alias])) {
                $targetDefinition = $container->getDefinition($id);

                foreach ($mapServices[$alias] as $eachMapServiceId) {
                    $targetDefinition->addMethodCall($this->targetMethod, array(new Reference($eachMapServiceId)));
                }
            }
        }
    }
}
