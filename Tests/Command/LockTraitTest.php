<?php

namespace Havvg\Bundle\DRYBundle\Tests\Command;

use Havvg\Bundle\DRYBundle\Tests\AbstractTest;
use Havvg\Bundle\DRYBundle\Tests\Fixtures\AbstractLockCommand;
use Havvg\Component\Lock\Acquirer\AcquirerInterface;
use Havvg\Component\Lock\Exception\ResourceLockedException;
use Havvg\Component\Lock\Repository\RepositoryInterface;
use Havvg\Component\Lock\Resource\ResourceInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Havvg\Bundle\DRYBundle\Command\LockTrait
 */
class LockTraitTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            self::markTestSkipped();
        }
    }

    public function testNoLockOptionDisablesLocking()
    {
        $repository = $this->getMock('Havvg\Component\Lock\Repository\RepositoryInterface');
        $repository
            ->expects($this->never())
            ->method('acquire')
        ;
        $repository
            ->expects($this->never())
            ->method('release')
        ;

        $tester = new CommandTester($this->getMockCommand($repository));
        $exit = $tester->execute(array(
            '--no-lock' => true,
        ));

        $this->assertEquals(0, $exit);
    }

    public function testLockedCommandIsNotExecuted()
    {
        $repository = $this->getMock('Havvg\Component\Lock\Repository\RepositoryInterface');
        $repository
            ->expects($this->once())
            ->method('acquire')
            ->will($this->throwException(new ResourceLockedException()))
        ;
        $repository
            ->expects($this->never())
            ->method('release')
        ;

        $tester = new CommandTester($this->getMockCommand($repository));

        $this->setExpectedException('RuntimeException');

        $tester->execute(array());
    }

    public function testLockIsReleased()
    {
        $lock = $this->getMock('Havvg\Component\Lock\Lock\LockInterface');

        $repository = $this->getMock('Havvg\Component\Lock\Repository\RepositoryInterface');
        $repository
            ->expects($this->once())
            ->method('acquire')
            ->will($this->returnValue($lock))
        ;
        $repository
            ->expects($this->once())
            ->method('release')
            ->with($lock)
        ;

        $tester = new CommandTester($this->getMockCommand($repository));
        $exit = $tester->execute(array());

        $this->assertEquals(0, $exit);
    }

    public function testExpiredLockIsReplacedByNewOne()
    {
        $lock = $this->getMock('Havvg\Component\Lock\Lock\LockInterface');

        $expiredLock = $this->getMock('Havvg\Component\Lock\Lock\ExpiringLockInterface');
        $expiredLock
            ->expects($this->once())
            ->method('getExpiresAt')
            ->will($this->returnValue(new \DateTime('-1 hour')))
        ;

        $repository = $this->getMock('Havvg\Component\Lock\Repository\RepositoryInterface');
        $repository
            ->expects($this->at(0))
            ->method('acquire')
            ->will($this->returnValue($expiredLock))
        ;
        $repository
            ->expects($this->at(1))
            ->method('release')
            ->with($expiredLock)
        ;
        $repository
            ->expects($this->at(2))
            ->method('acquire')
            ->will($this->returnValue($lock))
        ;
        $repository
            ->expects($this->at(3))
            ->method('release')
            ->with($lock)
        ;

        $tester = new CommandTester($this->getMockCommand($repository));
        $exit = $tester->execute(array());

        $this->assertEquals(0, $exit);
    }

    /**
     * @param RepositoryInterface    $repository
     * @param ResourceInterface|null $resource
     * @param AcquirerInterface|null $acquirer
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractLockCommand
     */
    protected function getMockCommand(RepositoryInterface $repository, ResourceInterface $resource = null, AcquirerInterface $acquirer = null)
    {
        $command = $this->getMockForAbstractClass('Havvg\Bundle\DRYBundle\Tests\Fixtures\AbstractLockCommand');
        $command
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository))
        ;

        if (!$acquirer) {
            $acquirer = $this->getMock('Havvg\Component\Lock\Acquirer\AcquirerInterface');
        }
        $command
            ->expects($this->any())
            ->method('getAcquirer')
            ->will($this->returnValue($acquirer))
        ;

        if (!$resource) {
            $resource = $this->getMock('Havvg\Component\Lock\Resource\ResourceInterface');
        }
        $command
            ->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($resource))
        ;

        return $command;
    }
}
