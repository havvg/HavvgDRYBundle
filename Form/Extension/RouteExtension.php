<?php

namespace Havvg\Bundle\DRYBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RouteExtension extends AbstractTypeExtension
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
    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['route'])) {
            return;
        }

        $builder->setAction($this->generator->generate($options['route'], $options['route_parameters'], $options['route_reference']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'route' => null,
            'route_parameters' => [],
            'route_reference' => UrlGeneratorInterface::ABSOLUTE_PATH,
        ]);

        $resolver->setAllowedTypes('route', ['null', 'string']);
        $resolver->setAllowedTypes('route_parameters', ['array']);

        $resolver->setAllowedValues('route_reference', [
            UrlGeneratorInterface::ABSOLUTE_PATH,
            UrlGeneratorInterface::ABSOLUTE_URL,
            UrlGeneratorInterface::RELATIVE_PATH,
            UrlGeneratorInterface::NETWORK_PATH,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\FormType';
    }
}
