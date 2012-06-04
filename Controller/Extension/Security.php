<?php

namespace Havvg\Bundle\DRYBundle\Controller\Extension;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

trait Security
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
     * Return the acl provider service.
     *
     * @return \Symfony\Component\Security\Acl\Model\MutableAclProviderInterface
     */
    public function getAclProvider()
    {
        return $this->get('security.acl.provider');
    }

    /**
     * Return the security context service.
     *
     * @return \Symfony\Component\Security\Core\SecurityContextInterface
     */
    public function getSecurityContext()
    {
        return $this->get('security.context');
    }

    /**
     * Check whether the current user is granted the given permission attributes on the object.
     *
     * @param string|array $attributes
     * @param object $object
     *
     * @return bool
     */
    public function isGranted($attributes, $object = null)
    {
        return $this->getSecurityContext()->isGranted($attributes, $object);
    }

    /**
     * Trigger 403 HTTP error on the given condition.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @param bool $condition
     * @param string|null $message An optional error message.
     *
     * @return void
     */
    public function denyAccessIf($condition, $message = null)
    {
        if ($condition) {
            throw new AccessDeniedHttpException($message);
        }
    }

    /**
     * Trigger 403 HTTP error unless the given condition is true.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @param bool $condition
     * @param string|null $message
     *
     * @return void
     */
    public function denyAccessUnless($condition, $message = null)
    {
        $this->denyAccessIf(!$condition, $message);
    }

    /**
     * Encodes the raw password.
     *
     * @param UserInterface $user The user to encode the password for.
     * @param string        $raw  The password to encode
     * @param string        $salt The salt
     *
     * @return string The encoded password
     */
    public function encodePassword(UserInterface $user, $raw, $salt)
    {
        return $this->get('security.encoder_factory')->getEncoder($user)->encodePassword($raw, $salt);
    }
}
