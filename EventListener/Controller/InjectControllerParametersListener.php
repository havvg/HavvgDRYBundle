<?php

namespace Havvg\Bundle\DRYBundle\EventListener\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class InjectControllerParametersListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $serviceIds = [];

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Registers a service id to be injected for the given classname.
     *
     * @param string $className
     * @param string $serviceId
     *
     * @return InjectControllerParametersListener
     */
    public function addServiceParameter($className, $serviceId)
    {
        $this->serviceIds[trim($className, '\\')] = $serviceId;
    }

    /**
     * Uses the registered services to be injected as request attributes.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        // Skip if there are no services registered at all.
        if (empty($this->serviceIds)) {
            return;
        }

        $controller = $event->getController();
        $request = $event->getRequest();

        if (is_array($controller)) {
            $controllerReflection = new \ReflectionMethod($controller[0], $controller[1]);
        } else {
            $controllerReflection = new \ReflectionFunction($controller);
        }

        foreach ($controllerReflection->getParameters() as $param) {
            // Not an object, skip.
            if (null === $param->getClass()) {
                continue;
            }

            // Skip unknown classes.
            if (empty($this->serviceIds[$param->getClass()->getName()])) {
                continue;
            }

            // The request already contains the attribute (provided by something else).
            if ($request->attributes->has($param->getName())) {
                continue;
            }

            // Retrieve and inject the registered service.
            $serviceId = $this->serviceIds[$param->getClass()->getName()];
            $service = $this->container->get($serviceId);

            $request->attributes->set($param->getName(), $service);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 32],
        ];
    }
}
