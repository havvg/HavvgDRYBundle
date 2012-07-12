<?php

namespace Havvg\Bundle\DRYBundle\Tests\Fixtures;

use Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AbstractTaggedMapCompilerPass;

class TaggedMapCompilerPass extends AbstractTaggedMapCompilerPass
{
    protected $mapServiceTag = 'acme.map_service';
    protected $targetServiceTag = 'acme.target_service';
    protected $targetMethod = 'addService';
}
