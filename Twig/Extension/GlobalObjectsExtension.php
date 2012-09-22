<?php

namespace Havvg\Bundle\DRYBundle\Twig\Extension;

class GlobalObjectsExtension extends \Twig_Extension
{
    protected $globals = array();

    /**
     * Add another global variable to the storage.
     *
     * @param string $key   The key represents the global variable name within your templates.
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
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'global_objects';
    }
}
