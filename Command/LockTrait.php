<?php

namespace Havvg\Bundle\DRYBundle\Command;

use Havvg\Component\Lock\Acquirer\AcquirerInterface;
use Havvg\Component\Lock\Exception\ExceptionInterface;
use Havvg\Component\Lock\Lock\ExpiringLockInterface;
use Havvg\Component\Lock\Lock\LockInterface;
use Havvg\Component\Lock\Repository\RepositoryInterface;
use Havvg\Component\Lock\Resource\ResourceInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The Lock enables a Command to be a lock acquirer and its own resource.
 *
 * This allows to have the same command running only once at a time.
 */
trait LockTrait
{
    /**
     * @var LockInterface|null
     */
    protected $lock = null;

    /**
     * Configures the Command to accept Lock options.
     */
    protected function configureLock()
    {
        $this->addOption('no-lock', null, InputOption::VALUE_NONE, 'Set this option to start the command without locking.');
        $this->addOption('lock-error-code', null, InputOption::VALUE_REQUIRED, 'The error code to be returned if the command is locked.', 1);
    }

    protected function initializeLock(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('no-lock')) {
            return;
        }

        try {
            $this->lock = $this->getRepository()->acquire($this->getAcquirer(), $this->getResource());

            if ($this->lock instanceof ExpiringLockInterface) {
                if ($this->lock->getExpiresAt() < new \DateTime()) {
                    $this->getRepository()->release($this->lock);

                    $this->lock = $this->getRepository()->acquire($this->getAcquirer(), $this->getResource());
                }
            }
        } catch (ExceptionInterface $e) {
            throw new \RuntimeException('The command could not run.', $input->getOption('lock-error-code'), $e);
        }
    }

    protected function releaseLock()
    {
        if ($this->lock instanceof LockInterface) {
            return $this->getRepository()->release($this->lock);
        }

        return true;
    }

    /**
     * Return the Repository to operate on the Locks.
     *
     * @return RepositoryInterface
     */
    abstract public function getRepository();

    /**
     * Return the Resource representing this Command.
     *
     * @return ResourceInterface
     */
    abstract public function getResource();

    /**
     * Return the Acquirer representing this Command.
     *
     * @return AcquirerInterface
     */
    abstract public function getAcquirer();

    /**
     * @see Symfony\Component\Console\Command\Command::addOption
     */
    abstract public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null);
}
