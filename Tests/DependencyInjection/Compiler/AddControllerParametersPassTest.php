<?php

namespace Havvg\Bundle\DRYBundle\Tests\DependencyInjection\Compiler;

use Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AddControllerParametersPass;
use Havvg\Bundle\DRYBundle\Tests\AbstractTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @covers Havvg\Bundle\DRYBundle\DependencyInjection\Compiler\AddControllerParametersPass
 */
class AddControllerParametersPassTest extends AbstractTest
{
    public function testArguments()
    {
        $targetService = new Definition();
        $targetService->setClass('Havvg\Bundle\DRYBundle\EventListener\Controller\InjectControllerParametersListener');

        $parameterService = new Definition();
        $parameterService->setClass('stdClass');
        $parameterService->addTag('havvg_dry.controller_parameter', ['class' => 'stdClass']);

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            'havvg_dry.inject_controller_parameters_listener' => $targetService,
            'acme.parameter_service' => $parameterService,
        ]);

        $builder->addCompilerPass(new AddControllerParametersPass());
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
        $targetMethodCalls = $builder->getDefinition('havvg_dry.inject_controller_parameters_listener')->getMethodCalls();
        self::assertNotEmpty($targetMethodCalls,
            'The extension service got method calls added.');
        self::assertEquals('addServiceParameter', $targetMethodCalls[0][0],
            'The extension service got an object added.');
        self::assertEquals('stdClass', $targetMethodCalls[0][1][0],
            'The extension service got the correct key of the object added.');
        self::assertEquals('acme.parameter_service', $targetMethodCalls[0][1][1],
            'The extension service got the correct object added.');
    }
}
