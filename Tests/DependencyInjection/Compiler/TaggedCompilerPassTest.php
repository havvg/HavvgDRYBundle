<?php

namespace Havvg\Bundle\DRYBundle\Tests\DependencyInjection\Compiler;

use Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\TaggedCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @covers \Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\TaggedCompilerPass
 */
class TaggedCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testTaggedService()
    {
        $targetService = new Definition();
        $targetService->setClass('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');

        $provider = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $taggedService = new Definition();
        $taggedService->setClass(get_class($provider));
        $taggedService->addTag('acme.service_tag');

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            'acme.target_service' => $targetService,
            'acme.tagged_service' => $taggedService,
        ]);

        $builder->addCompilerPass(new TaggedCompilerPass('acme.target_service', 'addService', 'acme.service_tag'));
        $builder->compile();

        /*
         * Schema:
         *
         * [0] The list of methods.
         *   [0] The name of the method to call.
         *   [1] The arguments to pass into the method call.
         *     [0] First argument to pass into the method call.
         *     ...
         */
        $targetMethodCalls = $builder->getDefinition('acme.target_service')->getMethodCalls();
        self::assertNotEmpty($targetMethodCalls,
            'The target service got method calls added.');
        self::assertEquals('addService', $targetMethodCalls[0][0],
            'The target service got a provider added.');
        self::assertEquals('acme.tagged_service', $targetMethodCalls[0][1][0],
            'The target service got the correct service added.');
    }
}
