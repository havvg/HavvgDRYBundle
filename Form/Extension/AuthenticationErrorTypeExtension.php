<?php

namespace Havvg\Bundle\DRYBundle\Form\Extension;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

final class AuthenticationErrorTypeExtension extends AbstractTypeExtension implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $extendedType;

    /**
     * Constructor.
     *
     * @param RequestStack $requestStack
     * @param string       $extendedType
     */
    public function __construct(RequestStack $requestStack, $extendedType)
    {
        $this->requestStack = $requestStack;
        $this->extendedType = $extendedType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this);
    }

    public function onPreSetData(FormEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($error = $this->getAuthenticationError($request)) {
            $event->getForm()->addError(new FormError($error->getMessage()));
        }
    }

    /**
     * Retrieves any authentication error of the given request.
     *
     * @param Request $request
     *
     * @return AuthenticationException|null
     */
    private function getAuthenticationError(Request $request)
    {
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            return $request->attributes->get(Security::AUTHENTICATION_ERROR);
        }

        $session = $request->getSession();
        if (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);

            return $error;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return $this->extendedType;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }
}
