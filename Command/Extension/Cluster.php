<?php

namespace Havvg\Bundle\DRYBundle\Command\Extension;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The Cluster enables a Command to be processed in parallel splitting items among the clustering processes.
 */
trait Cluster
{
    /**
     * The size of the cluster.
     *
     * This number represents the amount of parallel running processes.
     *
     * @var int
     */
    protected $clusterSize = 1;

    /**
     * The currently active cluster.
     *
     * This number represents the cluster instance.
     * There are exactly X clusters, where X is the cluster size.
     *
     * @var int
     */
    protected $cluster = 1;

    /**
     * Configures the Command to accept the cluster options.
     *
     * These options are configured
     *
     * * cluster-size: Represents the size of the cluster.
     * * cluster: The currently active cluster instance.
     */
    protected function configureCluster()
    {
        $this->addOption('cluster-size', null, InputOption::VALUE_REQUIRED, 'The size of the cluster. It defines the amount of members running this command in a cluster.', 1);
        $this->addOption('cluster', null, InputOption::VALUE_REQUIRED, 'The active cluster. This identifies a member of the cluster, in range from 1 to cluster-size.', 1);
    }

    protected function initializeCluster(InputInterface $input, OutputInterface $output)
    {
        $clusterSize = (int) $input->getOption('cluster-size');
        $cluster = (int) $input->getOption('cluster');

        if ($clusterSize and !$cluster) {
            throw new \InvalidArgumentException("The 'cluster' option is missing. A cluster-size is provided, but the current cluster is not defined.");
        }

        if ($cluster < 1 or $cluster > $clusterSize) {
            throw new \OutOfRangeException(sprintf("The 'cluster' option is invalid. It has to be in range 1..%d", $clusterSize));
        }

        $this->clusterSize = $clusterSize;
        $this->cluster = $cluster;
    }

    /**
     * Check whether the given item is processed by this cluster.
     *
     * This method is safe for non-clustered usage.
     * If you don't pass any options, a 1:1 cluster is given and the current process will process any item.
     *
     * @param int $item A numeric representation of the item to check.
     *                  This may for example be an ID of a record to process.
     *
     * @return bool
     */
    protected function isClusterProcessing($item)
    {
        return ($item % $this->getClusterSize() == ($this->getCluster() - 1));
    }

    /**
     * @see Symfony\Component\Console\Command\Command::addOption
     */
    abstract public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null);

    public function getCluster()
    {
        return $this->cluster;
    }

    public function getClusterSize()
    {
        return $this->clusterSize;
    }
}
