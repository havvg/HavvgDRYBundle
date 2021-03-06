<?php

namespace Havvg\Bundle\DRYBundle\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContainerConfigurationRegistry
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @see setDefaultOptions
     *
     * @param KernelInterface      $kernel   The kernel to load the configuration for.
     * @param array                $options  The options applied to this registry.
     * @param OptionsResolver|null $resolver An optional resolver, if none is given an empty one will be used.
     */
    public function __construct(KernelInterface $kernel, array $options = [], OptionsResolver $resolver = null)
    {
        $this->kernel = $kernel;

        if (!$resolver) {
            $resolver = new OptionsResolver();
        }

        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Registers the container configuration based on the provided configuration.
     *
     * @param LoaderInterface $loader
     * @param string          $type
     */
    public function register(LoaderInterface $loader, $type = 'yml')
    {
        $configDir = $this->options['config_dir'];

        /*
         * Load all globally defined configuration files.
         */
        $global = Finder::create()
            ->files()
            ->name('*.'.$type)
            ->in($configDir)
            ->depth('< 1')
        ;
        /* @var $eachFile \SplFileInfo */
        foreach ($global as $eachFile) {
            $loader->load($eachFile->getRealPath());
        }

        /*
         * Load all bundle specific configuration files.
         */
        if ($this->options['load_global_bundles']) {
            $bundles = Finder::create()
                ->files()
                ->name('*.'.$type)
                ->in($configDir.'/'.$this->options['bundles_dir_name'])
            ;
            foreach ($bundles as $eachFile) {
                $loader->load($eachFile->getRealPath());
            }
        }

        /*
         * Load all configuration files defining services.
         */
        $servicesDir = $configDir.'/'.$this->options['services_dir_name'];
        if (is_dir($servicesDir)) {
            $services = Finder::create()
                ->files()
                ->name('*.'.$type)
                ->in($configDir.'/'.$this->options['services_dir_name'])
            ;
            foreach ($services as $eachFile) {
                $loader->load($eachFile->getRealPath());
            }
        }

        /*
         * Populate configuration with global defaults.
         */
        if ($this->options['load_global_env_defaults']) {
            $defaultEnv = Finder::create()
                ->files()
                ->name('*.'.$type)
                ->in($this->options['env_global_config_dir'])
                ->depth('< 1')
            ;
            foreach ($defaultEnv as $eachFile) {
                $loader->load($eachFile->getRealPath());
            }
        }

        /*
         * Load environment specific configuration.
         */
        $envConfigDir = $this->options['env_config_dir'];

        /*
         * Load all environment specific bundle configuration files.
         */
        if ($this->options['allow_env_bundles'] && is_dir($envConfigDir.'/'.$this->options['bundles_dir_name'])) {
            $envBundles = Finder::create()
                ->files()
                ->name('*.'.$type)
                ->in($envConfigDir.'/'.$this->options['bundles_dir_name'])
            ;
            foreach ($envBundles as $eachFile) {
                $loader->load($eachFile->getRealPath());
            }
        }

        foreach ($this->options['config_files'] as $eachFilename) {
            $this->loadIfExists($loader, sprintf('%s/%s.%s', $envConfigDir, $eachFilename, $type));
        }
    }

    /**
     * Sets the default options for the registry.
     *
     * The default setup results in a configuration directory structure like:
     *
     * The global configuration files:
     *
     * app/config/
     *           /security.yml
     *           /propel.yml
     *           /some_other_global.yml
     *
     * The services being loaded, recursively:
     *
     * app/config/services/
     *                    /filesystem.yml
     *                    /menu.yml
     *                    /form/
     *                         /extension.yml
     *                         /type.yml
     *                         /custom.yml
     *
     * The default bundle configurations to load:
     *
     * app/config/bundles/
     *                   /fos_user.yml
     *                   /liip_imagine.yml
     *
     * The default environment configuration, all files will be loaded:
     *
     * app/config/environments/
     *                        /parameters.yml
     *                        /config.yml
     *
     * The environment specific configuration, e.g. overwriting the defaults.
     * Those files are the supplied in order to load.
     *
     * app/config/environments/dev/
     *                            /parameters.yml
     *                            /config.yml
     *                            /local.yml
     *
     * The environment specific bundles configuration, e.g. more bundles than other environments:
     *
     * app/config/environments/dev/bundles/
     *                                    /havvg_jasmine.yml
     *
     * @param OptionsResolver $resolver
     */
    protected function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // The environment to load configuration for.
            'env' => $this->kernel->getEnvironment(),

            // The root configuration directory.
            'config_dir' => $this->kernel->getRootDir().'/config',
            // The configuration directory where base configuration for environments are stored.
            'env_global_config_dir' => $this->kernel->getRootDir().'/config/environments',
            // The configuration directory of the current environment.
            'env_config_dir' => $this->kernel->getRootDir().'/config/environments/'.$this->kernel->getEnvironment(),

            // The directory name where to load services from.
            'services_dir_name' => 'services',
            // The directory name where to load bundle configurations from.
            'bundles_dir_name' => 'bundles',

            // Whether to load files from 'env_global_config_dir' prior to loading 'env_config_dir'.
            'load_global_env_defaults' => true,

            // Whether to load the global bundles configuration.
            'load_global_bundles' => true,
            // Whether additional bundles are allowed in environments.
            'allow_env_bundles' => true,

            /*
             * An ordered list of configuration files to load within the environment.
             * All files are optional.
             */
            'config_files' => [
                'parameters',
                'config',
                'local',
            ],
        ]);

        $resolver->setAllowedTypes('env', ['string']);

        $resolver->setAllowedTypes('config_dir', ['string']);
        $resolver->setAllowedTypes('env_global_config_dir', ['string']);
        $resolver->setAllowedTypes('env_config_dir', ['string']);

        $resolver->setAllowedTypes('services_dir_name', ['string']);
        $resolver->setAllowedTypes('bundles_dir_name', ['string']);

        $resolver->setAllowedTypes('load_global_env_defaults', ['bool']);
        $resolver->setAllowedTypes('load_global_bundles', ['bool']);
        $resolver->setAllowedTypes('allow_env_bundles', ['bool']);

        $resolver->setAllowedTypes('config_files', ['array']);
    }

    /**
     * Loads the given file, if it exsists, skips otherwise.
     *
     * @param LoaderInterface $loader
     * @param string          $filename
     */
    protected function loadIfExists(LoaderInterface $loader, $filename)
    {
        if (file_exists($filename)) {
            $loader->load($filename);
        }
    }
}
