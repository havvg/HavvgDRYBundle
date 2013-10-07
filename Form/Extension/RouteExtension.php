<?php

namespace Havvg\Bundle\DRYBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouteExtension extends AbstractTypeExtension
{
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['route'])) {
            return;
        }

        $builder->setAction($this->generator->generate($options['route'], $options['route_parameters'], $options['route_reference']));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'route' => null,
            'route_parameters' => array(),
            'route_reference' => UrlGeneratorInterface::ABSOLUTE_PATH,
        ));

        $resolver->setAllowedTypes(array(
            'route' => array('null', 'string'),
            'route_parameters' => array('array'),
        ));

        $resolver->setAllowedValues(array(
            'route_reference' => array(
                UrlGeneratorInterface::ABSOLUTE_PATH,
                UrlGeneratorInterface::ABSOLUTE_URL,
                UrlGeneratorInterface::RELATIVE_PATH,
                UrlGeneratorInterface::NETWORK_PATH,
            ),
        ));
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
