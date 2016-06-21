<?php

namespace Havvg\Bundle\DRYBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TranslationDomainTypeExtension extends AbstractTypeExtension
{
    /**
     * @var string
     */
    private $domain;

    /**
     * Constructor.
     *
     * @param string $domain
     */
    public function __construct($domain)
    {
        $this->domain = (string) $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => $this->domain,
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
