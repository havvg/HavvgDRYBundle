<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Compiler;

/**
 * A convenience class for simple tagged map compiler pass scenarios.
 */
class TaggedMapCompilerPass extends AbstractTaggedMapCompilerPass
{
    /**
     * Constructor.
     *
     * @param string $targetServiceTag
     * @param string $targetMethod
     * @param string $mapServiceTag
     */
    public function __construct($targetServiceTag, $targetMethod, $mapServiceTag)
    {
        $this->targetServiceTag = $targetServiceTag;
        $this->targetMethod = $targetMethod;
        $this->mapServiceTag = $mapServiceTag;
    }
}
