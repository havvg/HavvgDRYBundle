<?php

namespace Havvg\Bundle\DRYBundle\Tests\DependencyInjection\Compiler;

use Havvg\Bundle\DRYBundle\Tests\AbstractTest;
use Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AddGlobalObjectsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @covers Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AddGlobalObjectsCompilerPass
 */
class AddGlobalObjectsCompilerPassTest extends AbstractTest
{
    public function testWithoutTargetService()
    {
        $builder = $this->getBuilderMock();
        $builder->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(false))
        ;
        $builder
            ->expects($this->never())
            ->method('findTaggedServiceIds')
        ;

        $compilerPass = new AddGlobalObjectsCompilerPass();
        $compilerPass->process($builder);
    }

    public function testWithoutTaggedServices()
    {
        $builder = $this->getBuilderMock();
        $builder
            ->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true))
        ;
        $builder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue(array()))
        ;

        $compilerPass = new AddGlobalObjectsCompilerPass();
        $compilerPass->process($builder);
    }

    public function testWithTaggedServices()
    {
        $extensionService = new Definition();
        $extensionService->setClass('Havvg\Bundle\DRYBundle\Twig\Extension\GlobalObjectsExtension');

        $globalObject = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $globalObjectService = new Definition();
        $globalObjectService->setClass(get_class($globalObject));
        $globalObjectService->addTag('havvg_dry.twig.global_object', array(
            'alias' => 'foobar',
        ));

        $other = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $otherService = new Definition();
        $otherService->setClass(get_class($other));
        $otherService->addTag('acme.different_tag');

        $builder = new ContainerBuilder();
        $builder->addDefinitions(array(
            'havvg_dry.twig.extension.global_objects' => $extensionService,
            'acme.global_object' => $globalObjectService,
            'acme.other_service' => $otherService,
        ));

        $builder->addCompilerPass(new AddGlobalObjectsCompilerPass());
        $builder->compile();

        $this->assertNotEmpty($builder->getServiceIds(),
            'The services have been injected.');
        $this->assertNotEmpty($builder->get('havvg_dry.twig.extension.global_objects'),
            'The extension service has been injected.');
        $this->assertNotEmpty($builder->get('acme.global_object'),
            'The global object service has been injected.');
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
        $targetMethodCalls = $builder->getDefinition('havvg_dry.twig.extension.global_objects')->getMethodCalls();
        $this->assertNotEmpty($targetMethodCalls,
            'The extension service got method calls added.');
        $this->assertEquals('addGlobal', $targetMethodCalls[0][0],
            'The extension service got an object added.');
        $this->assertEquals('foobar', $targetMethodCalls[0][1][0],
            'The extension service got the correct key of the object added.');
        $this->assertEquals('acme.global_object', $targetMethodCalls[0][1][1],
            'The extension service got the correct object added.');
        $this->assertCount(1, $targetMethodCalls,
            'The other service has not been added.');
    }

    public function testWithoutAlias()
    {
        $extensionService = new Definition();
        $extensionService->setClass('Havvg\Bundle\DRYBundle\Twig\Extension\GlobalObjectsExtension');

        $globalObject = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\TargetService');
        $globalObjectService = new Definition();
        $globalObjectService->setClass(get_class($globalObject));
        $globalObjectService->addTag('havvg_dry.twig.global_object');

        $builder = new ContainerBuilder();
        $builder->addDefinitions(array(
            'havvg_dry.twig.extension.global_objects' => $extensionService,
            'acme.global_object' => $globalObjectService,
        ));
        $builder->addCompilerPass(new AddGlobalObjectsCompilerPass());

        $this->setExpectedException('LogicException');

        $builder->compile();
    }
}
