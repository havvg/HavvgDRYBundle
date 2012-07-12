<?php

namespace Havvg\Bundle\DRYBundle\Tests\DependencyInjection\Compiler;

use Havvg\Bundle\DRYBundle\Tests\AbstractTest;
use Havvg\Bundle\DRYBundle\Tests\Fixtures\TaggedMapCompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @covers Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AbstractTaggedMapCompilerPass
 */
class AbstractTaggedMapCompilerPassTest extends AbstractTest
{
    public function testWithoutMapServices()
    {
        $builder = $this->getBuilderMock();
        $builder->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('acme.map_service')
            ->will($this->returnValue(array()))
        ;

        $compilerPass = new TaggedMapCompilerPass();
        $compilerPass->process($builder);
    }

    public function testWithTaggedServices()
    {
        // The service which mapped services will be targeting.
        $targetService = new Definition();
        $targetService->setClass('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $targetService->addTag('acme.target_service', array('alias' => 'identifier'));

        // The service to be mapped to the target service.
        $map = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $mapService = new Definition();
        $mapService->setClass(get_class($map));
        $mapService->addTag('acme.map_service', array('target' => 'identifier'));

        // Another mapped service, but with undefined target service.
        $other = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $otherService = new Definition();
        $otherService->setClass(get_class($other));
        $otherService->addTag('acme.map_service', array('target' => 'undefined'));

        $builder = new ContainerBuilder();
        $builder->addDefinitions(array(
            'acme.target_service' => $targetService,
            'acme.map_service' => $mapService,
            'acme.other_service' => $otherService,
        ));

        $builder->addCompilerPass(new TaggedMapCompilerPass());
        $builder->compile();

        $this->assertNotEmpty($builder->getServiceIds(),
            'The services have been injected.');
        $this->assertNotEmpty($builder->get('acme.target_service'),
            'The target service has been injected.');
        $this->assertNotEmpty($builder->get('acme.map_service'),
            'The map service has been injected.');
        $this->assertNotEmpty($builder->get('acme.other_service'),
            'The other service has been injected.');

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
        $this->assertNotEmpty($targetMethodCalls,
            'The target service got method calls added.');
        $this->assertEquals('addService', $targetMethodCalls[0][0],
            'The target service got a mapped service added.');
        $this->assertEquals('acme.map_service', $targetMethodCalls[0][1][0],
            'The target service got the correct mapped service added.');
        $this->assertCount(1, $targetMethodCalls,
            'The other service has not been added.');
    }
}
