<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractTaggedMapCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $mapServiceTag;

    /**
     * @var string
     */
    protected $targetServiceTag;

    /**
     * @var string
     */
    protected $targetMethod;

    /**
     * References mapped services with tagged services.
     *
     * The compiler uses two tags to identify two types of services.
     * A "target service" is a receiver of multiple "mapped services".
     *
     * The mapped service tag defines a "target" property which maps to the "alias" property of the target service tag.
     *
     * The argument list defaults to the mapped service.
     *
     * @see AbstractTaggedMapCompilerPass::getTargetServiceTag
     * @see AbstractTaggedMapCompilerPass::getTargetMethod
     * @see AbstractTaggedMapCompilerPass::getMapServiceTag
     * @see AbstractTaggedMapCompilerPass::getArguments
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $mapServices = [];
        foreach ($container->findTaggedServiceIds($this->getMapServiceTag()) as $id => $tags) {
            foreach ($tags as $eachTag) {
                $targetId = $eachTag['target'];

                if (empty($mapServices[$targetId])) {
                    $mapServices[$targetId] = [];
                }

                $mapServices[$targetId][] = $id;
            }
        }

        if (empty($mapServices)) {
            return;
        }

        foreach ($container->findTaggedServiceIds($this->getTargetServiceTag()) as $id => $tags) {
            foreach ($tags as $eachTag) {
                $alias = $eachTag['alias'];

                if (empty($mapServices[$alias])) {
                    continue;
                }

                $targetDefinition = $container->getDefinition($id);
                foreach ($mapServices[$alias] as $eachMapServiceId) {
                    $targetDefinition->addMethodCall($this->getTargetMethod(), $this->getArguments($eachMapServiceId, $container, $eachTag));
                }
            }
        }
    }

    /**
     * Returns the argument list on the target method for a single service.
     *
     * @param string           $id
     * @param ContainerBuilder $container
     * @param array            $tag
     *
     * @return array
     */
    protected function getArguments($id, ContainerBuilder $container, array $tag)
    {
        return [new Reference($id)];
    }

    /**
     * Returns the name of the tag to identify mapped services.
     *
     * @return string
     */
    protected function getMapServiceTag()
    {
        return $this->mapServiceTag;
    }

    /**
     * Returns the name of the method to be called on target services.
     *
     * @return string
     */
    protected function getTargetMethod()
    {
        return $this->targetMethod;
    }

    /**
     * Returns the name of the target service to attach mapped services to using the target method.
     *
     * @return string
     */
    protected function getTargetServiceTag()
    {
        return $this->targetServiceTag;
    }
}
