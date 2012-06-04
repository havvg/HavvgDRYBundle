<?php

namespace Havvg\Bundle\DRYBundle\DependencyInjection\Loader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use Symfony\Component\Finder\Finder;

/**
 * The ServicesLoader loads all configuration files from a directory (e.g. "Resources/config/services").
 */
class ServicesLoader
{
    /**
     * @var \SplFileInfo
     */
    protected $serviceDirectory;

    /**
     * @var ContainerBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $names;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var FileLoader
     */
    protected $fileLoader;

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * Constructor.
     *
     * @param \SplFileInfo|string $serviceDirectory The directory of the services configuration files.
     * @param ContainerBuilder    $builder          The builder to add loaded services to.
     * @param string              $names            The file name string to be passed to the finder.
     */
    public function __construct($serviceDirectory, ContainerBuilder $builder, $names = '*.yml')
    {
        if (!$serviceDirectory instanceof \SplFileInfo) {
            $serviceDirectory = new \SplFileInfo($serviceDirectory);
        }

        $this->serviceDirectory = $serviceDirectory;
        $this->builder = $builder;
        $this->names = $names;
    }

    /**
     * Load the files within the service directory.
     */
    public function load()
    {
        $this->initialize();

        $files = $this->finder
            ->files()
            ->in($this->serviceDirectory->getRealPath())
            ->name($this->names)
        ;

        foreach ($files as $eachFile) {
            /* @var $eachFile \SplFileInfo */
            $this->fileLoader->load($eachFile->getRealPath());
        }
    }

    /**
     * Set the Finder to be used to find files.
     *
     * @param Finder $finder
     *
     * @return ServicesLoader
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;

        return $this;
    }

    /**
     * Set the file loader to be used.
     *
     * @param FileLoader $fileLoader
     *
     * @return ServicesLoader
     */
    public function setFileLoader(FileLoader $fileLoader)
    {
        $this->fileLoader = $fileLoader;

        return $this;
    }

    /**
     * Set the file locator to be used.
     *
     * @param FileLocator $fileLocator
     *
     * @return ServicesLoader
     */
    public function setFileLocator(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;

        return $this;
    }

    /**
     * Initialize this loader.
     *
     * This will initialize the defaults for basic usage.
     */
    protected function initialize()
    {
        if (null === $this->finder) {
            $this->setFinder(Finder::create());
        }

        if (null === $this->fileLocator) {
            $this->setFileLocator(new FileLocator($this->serviceDirectory));
        }

        if (null === $this->fileLoader) {
            $this->setFileLoader(new YamlFileLoader($this->builder, $this->fileLocator));
        }
    }
}
