<?php

namespace Havvg\Bundle\DRYBundle\Controller;

trait SessionTrait
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
     * Return the session service.
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    public function getSession()
    {
        return $this->get('session');
    }

    /**
     * Sets a flash message.
     *
     * @param string $type
     * @param string $value
     */
    public function setFlash($type, $value)
    {
        $this->getSession()->getFlashBag()->set($type, $value);
    }

    /**
     * Add a flash message.
     *
     * @param string $type
     * @param string $value
     */
    public function addFlash($type, $value)
    {
        $this->getSession()->getFlashBag()->add($type, $value);
    }

    /**
     * Checks whether a flash message exists.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasFlash($type)
    {
        return $this->getSession()->getFlashBag()->has($type);
    }

    /**
     * Gets a flash message.
     *
     * @param string      $name
     * @param string|null $default
     *
     * @return string
     */
    public function getFlash($type, $default = null)
    {
        return $this->getSession()->getFlashBag()->get($type, $default);
    }
}
