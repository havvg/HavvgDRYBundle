<?php

namespace Havvg\Bundle\DRYBundle\Tests\DependencyInjection\Loader;

use Havvg\Bundle\DRYBundle\Tests\AbstractTest;

use Havvg\Bundle\DRYBundle\DependencyInjection\Loader\ServicesLoader;

/**
 * @covers Havvg\Bundle\DRYBundle\DependencyInjection\Loader\ServicesLoader
 */
class ServicesLoaderTest extends AbstractTest
{
    public function testLoadMultipleFiles()
    {
        $directory = $this->getFixturesDirectory().'/Resources/config/services';
        $builder = $this->getBuilderMock();
        $builder
            ->expects($this->exactly(2))
            ->method('addResource')
        ;

        $loader = new ServicesLoader($directory, $builder);
        $loader->load();
    }
}
