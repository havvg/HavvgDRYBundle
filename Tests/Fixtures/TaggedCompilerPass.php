<?php

namespace Havvg\Bundle\DRYBundle\Tests\Fixtures;

use Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AbstractTaggedCompilerPass;

class TaggedCompilerPass extends AbstractTaggedCompilerPass
{
    protected $tag = 'acme.service_tag';
    protected $targetService = 'acme.target_service';
    protected $targetMethod = 'addService';
}
