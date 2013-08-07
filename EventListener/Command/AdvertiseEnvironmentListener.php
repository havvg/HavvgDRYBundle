<?php

namespace Havvg\Bundle\DRYBundle\EventListener\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdvertiseEnvironmentListener implements EventSubscriberInterface
{
    protected $advertisement = 'Running in <info>%1$s</info> environment with debug <info>%2$s</info>';

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $application = $event->getCommand()->getApplication();
        if ($application instanceof Application) {
            $kernel = $application->getKernel();
            $event->getOutput()->writeln(sprintf($this->getAdvertisement(), $kernel->getEnvironment(), var_export($kernel->isDebug(), true)));
        }
    }

    public function setAdvertisement($advertisement)
    {
        $this->advertisement = $advertisement;

        return $this;
    }

    public function getAdvertisement()
    {
        return $this->advertisement;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ConsoleEvents::COMMAND => 'onConsoleCommand',
        );
    }
}
