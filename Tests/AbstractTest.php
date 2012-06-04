<?php

namespace Havvg\Bundle\DRYBundle\Tests;

use Havvg\Bundle\DRYBundle\Tests\Fixtures\Controller;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Return a mock object for the DI ContainerInterface.
     *
     * @param array $services A key-value list of services the container contains.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContainerMock(array $services)
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->atLeastOnce())
            ->method('get')
            ->will($this->returnCallback(function($service) use ($services) {
                return $services[$service];
            }))
        ;

        return $container;
    }

    /**
     * Return a mock object for the DI ContainerBuilder.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getBuilder()
    {
        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        return $builder;
    }

    protected function createController()
    {
        return new Controller();
    }
}
