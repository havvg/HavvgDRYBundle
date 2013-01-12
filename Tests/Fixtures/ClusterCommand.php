<?php

namespace Havvg\Bundle\DRYBundle\Tests\Fixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Havvg\Bundle\DRYBundle\Command\Extension\Cluster;

class ClusterCommand extends Command
{
    use Cluster;

    protected function configure()
    {
        $this
            ->setName('test:cluster')
            ->addArgument('record', InputArgument::REQUIRED)
        ;

        $this->configureCluster();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->initializeCluster($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->isClusterProcessing($input->getArgument('record'))) {
            return 0;
        } else {
            return 1;
        }
    }
}
