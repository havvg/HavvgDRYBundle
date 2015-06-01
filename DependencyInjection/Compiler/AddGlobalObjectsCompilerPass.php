<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddGlobalObjectsCompilerPass implements CompilerPassInterface
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
     * References tagged services with the global object twig extension.
     *
     * @see Havvg\Bundle\DRYBundle\Twig\Extension\GlobalObjectsExtension
     *
     * @param ContainerBuilder $container
     */
    final public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->targetService)) {
            return;
        }

        foreach ($container->findTaggedServiceIds($this->tag) as $id => $tags) {
            $key = null;
            foreach ($tags as $eachTag) {
                if (!empty($eachTag['alias'])) {
                    $key = $eachTag['alias'];

                    break;
                }
            }

            if (!$key) {
                throw new \LogicException(sprintf('There is no key given for the "%s" global object service.', $id));
            }

            $container
                ->getDefinition($this->targetService)
                ->addMethodCall($this->targetMethod, array($key, new Reference($id)))
            ;
        }
    }
}
