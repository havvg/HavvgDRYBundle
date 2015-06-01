<?php

namespace Havvg\Bundle\DRYBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouteExtension extends AbstractTypeExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $generator
     */
    final public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    final public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['route'])) {
            return;
        }

        $builder->setAction($this->generator->generate($options['route'], $options['route_parameters'], $options['route_reference']));
    }

    /**
     * {@inheritdoc}
     */
    final public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'route' => null,
            'route_parameters' => array(),
            'route_reference' => UrlGeneratorInterface::ABSOLUTE_PATH,
        ));

        $resolver->setAllowedTypes('route', array('null', 'string'));
        $resolver->setAllowedTypes('route_parameters', array('array'));

        $resolver->setAllowedValues('route_reference', array(
            UrlGeneratorInterface::ABSOLUTE_PATH,
            UrlGeneratorInterface::ABSOLUTE_URL,
            UrlGeneratorInterface::RELATIVE_PATH,
            UrlGeneratorInterface::NETWORK_PATH,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
