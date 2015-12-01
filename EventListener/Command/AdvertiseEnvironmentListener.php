<?php

namespace Havvg\Bundle\DRYBundle\EventListener\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdvertiseEnvironmentListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $advertisement = 'Running in <info>%1$s</info> environment with debug <info>%2$s</info>';

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $application = $event->getCommand()->getApplication();
        if ($application instanceof Application) {
            $kernel = $application->getKernel();
            $event->getOutput()->writeln(sprintf($this->getAdvertisement(), $kernel->getEnvironment(), var_export($kernel->isDebug(), true)));
        }
    }

    /**
     * Changes the advertisement being rendered when executing a command.
     *
     * @param string $advertisement
     *
     * @return AdvertiseEnvironmentListener
     */
    public function setAdvertisement($advertisement)
    {
        $this->advertisement = $advertisement;

        return $this;
    }

    /**
     * Returns the advertisement to be rendered when executing a command.
     *
     * @return string
     */
    public function getAdvertisement()
    {
        return $this->advertisement;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleCommand',
        ];
    }
}
