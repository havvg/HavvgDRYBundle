<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class AddGlobalObjectsPass extends AbstractTaggedCompilerPass
{
    /**
     * The tag for global twig objects.
     *
     * @var string
     */
    protected $tag = 'havvg_dry.twig.global_object';

    /**
     * The service name of the twig extension.
     *
     * @var string
     */
    protected $targetService = 'havvg_dry.twig.extension.global_objects';

    /**
     * @var string
     */
    protected $targetMethod = 'addGlobal';

    /**
     * {@inheritdoc}
     */
    protected function getArguments($id, ContainerBuilder $container, array $tag)
    {
        if (empty($tag['alias'])) {
            throw new \LogicException(sprintf('There is no alias given for the "%s" global object service.', $id));
        }

        return [$tag['alias'], new Reference($id)];
    }
}
