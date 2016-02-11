<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

/**
 * A convenience class for simple tagged compiler pass scenarios.
 */
class TaggedCompilerPass extends AbstractTaggedCompilerPass
{
    /**
     * Constructor.
     *
     * @param string $targetService The service to add the method calls to.
     * @param string $targetMethod  The name of the method to call.
     * @param string $tag           The name of the tag to find services by.
     */
    public function __construct($targetService, $targetMethod, $tag)
    {
        $this->targetService = $targetService;
        $this->targetMethod = $targetMethod;
        $this->tag = $tag;
    }
}
