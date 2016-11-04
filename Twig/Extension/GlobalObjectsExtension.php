<?php

namespace Havvg\Bundle\DRYBundle\Twig\Extension;

final class GlobalObjectsExtension implements \Twig_ExtensionInterface, \Twig_Extension_GlobalsInterface, \Twig_Extension_InitRuntimeInterface
{
    use ExtensionTrait;

    /**
     * A list of global objects to make available in Twig templates.
     *
     * @var object[]
     */
    private $globals = [];

    /**
     * Adds another global variable to the storage.
     *
     * @param string $key   The key represents the global variable name within the templates.
     * @param mixed  $value The actual value to be addressed by the key.
     *
     * @return GlobalObjectsExtension
     */
    public function addGlobal($key, $value)
    {
        $this->globals[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'global_objects';
    }
}
