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
            ->will($this->returnValue([]))
        ;

        $compilerPass = new TaggedMapCompilerPass();
        $compilerPass->process($builder);
    }

    public function testWithTaggedServices()
    {
        // The service which mapped services will be targeting.
        $targetService = new Definition();
        $targetService->setClass('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $targetService->addTag('acme.target_service', ['alias' => 'identifier']);

        // The service to be mapped to the target service.
        $map = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $mapService = new Definition();
        $mapService->setClass(get_class($map));
        $mapService->addTag('acme.map_service', ['target' => 'identifier']);

        // Another mapped service, but with undefined target service.
        $other = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $otherService = new Definition();
        $otherService->setClass(get_class($other));
        $otherService->addTag('acme.map_service', ['target' => 'undefined']);

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            'acme.target_service' => $targetService,
            'acme.map_service' => $mapService,
            'acme.other_service' => $otherService,
        ]);

        $builder->addCompilerPass(new TaggedMapCompilerPass());
        $builder->compile();

        self::assertNotEmpty($builder->getServiceIds(),
            'The services have been injected.');
        self::assertNotEmpty($builder->get('acme.target_service'),
            'The target service has been injected.');
        self::assertNotEmpty($builder->get('acme.map_service'),
            'The map service has been injected.');
        self::assertNotEmpty($builder->get('acme.other_service'),
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
        self::assertNotEmpty($targetMethodCalls,
            'The target service got method calls added.');
        self::assertEquals('addService', $targetMethodCalls[0][0],
            'The target service got a mapped service added.');
        self::assertEquals('acme.map_service', $targetMethodCalls[0][1][0],
            'The target service got the correct mapped service added.');
        self::assertCount(1, $targetMethodCalls,
            'The other service has not been added.');
    }

    public function testMultipleTargets()
    {
        // The services which mapped services will be targeting.
        $target1Service = new Definition();
        $target1Service->setClass('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $target1Service->addTag('acme.target_service', ['alias' => 'identifier']);

        $target2Service = new Definition();
        $target2Service->setClass('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $target2Service->addTag('acme.target_service', ['alias' => 'second_identifier']);

        // The service to be mapped to the target service.
        $map = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $mapService = new Definition();
        $mapService->setClass(get_class($map));
        $mapService->addTag('acme.map_service', ['target' => 'identifier']);
        $mapService->addTag('acme.map_service', ['target' => 'second_identifier']);

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            'acme.target_service' => $target1Service,
            'acme.second_target_service' => $target2Service,
            'acme.map_service' => $mapService,
        ]);

        $builder->addCompilerPass(new TaggedMapCompilerPass());
        $builder->compile();

        self::assertNotEmpty($builder->getServiceIds(),
            'The services have been injected.');
        self::assertNotEmpty($builder->get('acme.target_service'),
            'The target service has been injected.');
        self::assertNotEmpty($builder->get('acme.second_target_service'),
            'The second target service has been injected.');
        self::assertNotEmpty($builder->get('acme.map_service'),
            'The map service has been injected.');

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
            'The target service got a mapped service added.');
        self::assertEquals('acme.map_service', $targetMethodCalls[0][1][0],
            'The target service got the correct mapped service added.');
        self::assertCount(1, $targetMethodCalls,
            'The other service has not been added.');

        $target2MethodCalls = $builder->getDefinition('acme.second_target_service')->getMethodCalls();
        self::assertNotEmpty($target2MethodCalls,
            'The target service got method calls added.');
        self::assertEquals('addService', $target2MethodCalls[0][0],
            'The target service got a mapped service added.');
        self::assertEquals('acme.map_service', $target2MethodCalls[0][1][0],
            'The target service got the correct mapped service added.');
        self::assertCount(1, $target2MethodCalls,
            'The other service has not been added.');
    }
}
