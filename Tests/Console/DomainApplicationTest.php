<?php

namespace Havvg\Bundle\DRYBundle\Tests\Console;

use Havvg\Bundle\DRYBundle\Console\DomainApplication;
use Havvg\Bundle\DRYBundle\Tests\Fixtures\DomainCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @covers \Havvg\Bundle\DRYBundle\Console\DomainApplication
 */
class DomainApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KernelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $kernel;

    /**
     * @var DomainApplication
     */
    private $application;

    protected function setUp()
    {
        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');

        $this->application = new DomainApplication($this->kernel);
    }

    public function testRegistersDomainBundlesOnly()
    {
        $bundles = [];

        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $bundle->expects($this->never())->method('registerCommands');
        $bundles[] = $bundle;

        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->never())->method('registerCommands');
        $bundles[] = $bundle;

        $bundle = $this->getMock('Havvg\Bundle\DRYBundle\Tests\Fixtures\DomainBundle');
        $bundle->expects($this->once())->method('registerCommands');
        $bundles[] = $bundle;

        $this->kernel->expects($this->once())->method('boot');
        $this->kernel->expects($this->any())->method('getContainer')->willReturn(new Container());
        $this->kernel->expects($this->once())->method('getBundles')->willReturn($bundles);

        $commands = $this->application->all();
    }

    public function testRegistersDomainCommandsOnly()
    {
        $domainCommand = new DomainCommand('domain:command');
        $command = new Command('fixtures:command');

        $container = new Container();
        $container->setParameter('console.command.ids', ['domain_command', 'another_command']);
        $container->set('domain_command', $domainCommand);
        $container->set('another_command', $command);

        $this->kernel->expects($this->once())->method('boot');
        $this->kernel->expects($this->any())->method('getContainer')->willReturn($container);
        $this->kernel->expects($this->once())->method('getBundles')->willReturn([]);

        $commands = $this->application->all();

        static::assertContains($domainCommand, $commands);
        static::assertNotContains($command, $commands);
    }
}
