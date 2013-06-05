<?php

namespace Havvg\Bundle\DRYBundle\Controller;

trait LogTrait
{
    /**
     * Gets a service by id.
     *
     * @param  string $id The service id
     *
     * @return object The service
     */
    abstract public function get($service);

    /**
     * Return the logger service.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * Log an alert.
     *
     * This log event will call the military!
     *
     * @param string $message
     * @param array $context
     */
    public function logAlert($message, array $context = array())
    {
        $this->getLogger()->alert($message, $context);
    }

    /**
     * Log a critical condition.
     *
     * Any critical condition triggers an immediate alert!
     *
     * @param string $message
     * @param array $context
     */
    public function logCritical($message, array $context = array())
    {
        $this->getLogger()->critical($message, $context);
    }

    /**
     * Log an error.
     *
     * @param string $message
     * @param array $context
     */
    public function logError($message, array $context = array())
    {
        $this->getLogger()->error($message, $context);
    }

    /**
     * Log a warning.
     *
     * A warning has to be reviewed, but no immediate action is required.
     *
     * @param string $message
     * @param array $context
     */
    public function logWarning($message, array $context = array())
    {
        $this->getLogger()->warning($message, $context);
    }

    /**
     * Log interesting events or other information.
     *
     * @param string $message
     * @param array $context
     */
    public function logInfo($message, array $context = array())
    {
        $this->getLogger()->info($message, $context);
    }

    /**
     * Log debug information.
     *
     * @param string $message
     * @param array $context
     */
    public function logDebug($message, array $context = array())
    {
        $this->getLogger()->debug($message, $context);
    }
}
