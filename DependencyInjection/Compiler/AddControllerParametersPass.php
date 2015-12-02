<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AddControllerParametersPass extends AbstractTaggedCompilerPass
{
    /**
     * @var string
     */
    protected $tag = 'havvg_dry.controller_parameter';

    /**
     * @var string
     */
    protected $targetService = 'havvg_dry.inject_controller_parameters_listener';

    /**
     * @var string
     */
    protected $targetMethod = 'addServiceParameter';

    /**
     * {@inheritdoc}
     */
    protected function getArguments($id, ContainerBuilder $container, array $tag)
    {
        if (empty($tag['class'])) {
            throw new \LogicException(sprintf('There is no class given for the "%s" service.', $id));
        }

        return [$tag['class'], $id];
    }
}
