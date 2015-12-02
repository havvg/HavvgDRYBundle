<?php

namespace Havvg\Bundle\DRYBundle\Tests\EventListener\Controller;

use Havvg\Bundle\DRYBundle\EventListener\Controller\InjectControllerParametersListener;
use Havvg\Bundle\DRYBundle\Tests\AbstractTest;
use Havvg\Bundle\DRYBundle\Tests\Fixtures\Controller;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @covers Havvg\Bundle\DRYBundle\EventListener\Controller\InjectControllerParametersListener
 */
class InjectControllerParametersListenerTest extends AbstractTest
{
    /**
     * @var ParameterBag
     */
    private $attributes;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var InjectControllerParametersListener
     */
    private $listener;

    protected function setUp()
    {
        $this->attributes = new ParameterBag();
        $this->container = new Container();

        $this->listener = new InjectControllerParametersListener($this->container);
    }

    public function testEventSubscription()
    {
        static::assertArrayHasKey(KernelEvents::CONTROLLER, InjectControllerParametersListener::getSubscribedEvents());
    }

    public function testInjectParameter()
    {
        $this->container->set('service_id', $service = new \stdClass());
        $this->listener->addServiceParameter('stdClass', 'service_id');

        $this->triggerFilterControllerEvent([$this, 'indexAction']);

        $this->assertRequestAttribute($service, 'service');
    }

    /**
     * @depends testInjectParameter
     */
    public function testAcceptLeadingNamespaceSeparator()
    {
        $this->container->set('service_id', $service = new \stdClass());
        $this->listener->addServiceParameter('\stdClass', 'service_id');

        $this->triggerFilterControllerEvent([$this, 'indexAction']);

        $this->assertRequestAttribute($service, 'service');
    }

    /**
     * @depends testInjectParameter
     */
    public function testInjectMultipleParameters()
    {
        $this->container->set('service_id', $service = new \stdClass());
        $this->listener->addServiceParameter('stdClass', 'service_id');

        $this->container->set('another_id', $object = new \ArrayObject());
        $this->listener->addServiceParameter('ArrayObject', 'another_id');

        $this->triggerFilterControllerEvent([$this, 'multipleAction']);

        $this->assertRequestAttribute($service, 'service');
        $this->assertRequestAttribute($object, 'object');
    }

    /**
     * @depends testInjectParameter
     */
    public function testInjectIntoClosure()
    {
        $this->container->set('service_id', $service = new \stdClass());
        $this->listener->addServiceParameter('stdClass', 'service_id');

        $this->triggerFilterControllerEvent(function (\stdClass $service) {});

        $this->assertRequestAttribute($service, 'service');
    }

    public function testSkipNonObjects()
    {
        $this->container->set('service_id', new \stdClass());
        $this->listener->addServiceParameter('stdClass', 'service_id');

        $this->triggerFilterControllerEvent([$this, 'scalarAction']);

        static::assertFalse($this->attributes->has('service'));
    }

    public function testSkipUnknownClasses()
    {
        $this->container->set('service_id', new \stdClass());
        $this->listener->addServiceParameter('stdClass', 'service_id');

        $this->triggerFilterControllerEvent([$this, 'unknownAction']);

        static::assertFalse($this->attributes->has('service'));
    }

    public function testDoesNotOverwriteExistingAttributes()
    {
        $this->container->set('service_id', new \stdClass());
        $this->listener->addServiceParameter('stdClass', 'service_id');

        $this->attributes->set('service', $service = new \stdClass());

        $this->triggerFilterControllerEvent([$this, 'indexAction']);

        $this->assertRequestAttribute($service, 'service');
    }

    /**
     * @depends testInjectParameter
     */
    public function testLeaveOptionalIfUnknown()
    {
        $this->triggerFilterControllerEvent([$this, 'optionalAction']);

        static::assertFalse($this->attributes->has('service'));
    }

    /**
     * Triggers the KernelEvents::CONTROLLER on the configured listener.
     */
    private function triggerFilterControllerEvent($callable)
    {
        $this->listener->onKernelController($this->createFilterControllerEvent($callable));
    }

    /**
     * Asserts that the given service is available on a specific attribute name.
     *
     * @param object $service
     * @param string $name
     */
    private function assertRequestAttribute($service, $name)
    {
        self::assertTrue($this->attributes->has($name), sprintf('The attribute "%s" has been set.', $name));
        self::assertSame($service, $this->attributes->get($name), sprintf('The service has been injected into "%s".', $name));
    }

    /**
     * Creates a filter controller event with the given callable as controller.
     *
     * @param callable $callable
     *
     * @return FilterControllerEvent
     */
    private function createFilterControllerEvent($callable)
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');

        $request = new Request();
        $request->attributes = $this->attributes;

        return new FilterControllerEvent($kernel, $callable, $request, HttpKernelInterface::MASTER_REQUEST);
    }

    // fixture methods
    public function scalarAction(array $service)
    {
    }
    public function unknownAction(\SplObjectStorage $service)
    {
    }
    public function indexAction(\stdClass $service)
    {
    }
    public function multipleAction(\stdClass $service, \ArrayObject $object)
    {
    }
    public function optionalAction(\stdClass $service = null)
    {
    }
}
