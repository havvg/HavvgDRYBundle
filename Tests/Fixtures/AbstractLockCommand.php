<?php

namespace Havvg\Bundle\DRYBundle\Tests\Fixtures;

use Havvg\Bundle\DRYBundle\Command\Extension\Lock;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractLockCommand extends Command
{
    use Lock;

    protected function configure()
    {
        $this->setName('test:lock');

        $this->configureLock();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->initializeLock($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->releaseLock();

        return 0;
    }
}
