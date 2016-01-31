<?php

namespace Havvg\Bundle\DRYBundle\Console;

use Havvg\Bundle\DRYBundle\Command\DomainCommandInterface;
use Havvg\Bundle\DRYBundle\DomainBundleInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

class DomainApplication extends Application
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var bool
     */
    private $commandsRegistered = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $this->registerCommands();

        return parent::get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function all($namespace = null)
    {
        $this->registerCommands();

        return parent::all($namespace);
    }

    /**
     * Registers all commands that belong to the domain.
     *
     * @see DomainBundleInterface
     * @see DomainCommandInterface
     */
    protected function registerCommands()
    {
        if ($this->commandsRegistered) {
            return;
        }

        $this->commandsRegistered = true;

        $this->kernel->boot();

        $container = $this->kernel->getContainer();

        foreach ($this->kernel->getBundles() as $bundle) {
            if (!$bundle instanceof DomainBundleInterface) {
                continue;
            }

            if ($bundle instanceof Bundle) {
                $bundle->registerCommands($this);
            }
        }

        if ($container->hasParameter('console.command.ids')) {
            foreach ($container->getParameter('console.command.ids') as $id) {
                $command = $container->get($id);

                if ($command instanceof DomainCommandInterface) {
                    $this->add($command);
                }
            }
        }
    }
}
