<?php

namespace Havvg\Bundle\DRYBundle\Tests\Command;

use Havvg\Bundle\DRYBundle\Tests\AbstractTest;
use Havvg\Bundle\DRYBundle\Tests\Fixtures\ClusterCommand;

use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Havvg\Bundle\DRYBundle\Command\ClusterTrait
 */
class ClusterTraitTest extends AbstractTest
{
    protected function setUp()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped();
        }
    }

    public function clusterProvider()
    {
        return array(
            // No actual clustering, single processing.
            array(1, 1, 1, 0),
            array(1, 1, 5, 0),
            array(1, 1, 17, 0),
            array(1, 1, 42, 0),
            array(1, 1, 1337, 0),

            // Two members in the cluster.
            array(2, 1, 1, 1),
            array(2, 1, 2, 0),
            array(2, 2, 1, 0),
            array(2, 2, 2, 1),

            // Three members in the cluster.
            array(3, 1, 1, 1),
            array(3, 1, 2, 1),
            array(3, 1, 3, 0),
            array(3, 2, 1, 0),
            array(3, 2, 2, 1),
            array(3, 2, 3, 1),
            array(3, 3, 1, 1),
            array(3, 3, 2, 0),
            array(3, 3, 3, 1),
        );
    }

    /**
     * @dataProvider clusterProvider
     */
    public function testCluster($size, $active, $record, $expected)
    {
        $tester = new CommandTester(new ClusterCommand());
        $exit = $tester->execute(array(
            'record' => $record,
            '--cluster-size' => $size,
            '--cluster' => $active,
        ));

        $this->assertEquals($expected, $exit);
    }

    public function testNoCluster()
    {
        $tester = new CommandTester(new ClusterCommand());
        $exit = $tester->execute(array(
            'record' => 5,
        ));

        $this->assertEquals(0, $exit);
    }

    public function testInvalidCluster()
    {
        $tester = new CommandTester(new ClusterCommand());

        $this->setExpectedException('InvalidArgumentException');

        $tester->execute(array(
            'record' => 8,
            '--cluster-size' => 5,
            '--cluster' => 0,
        ));
    }

    public function testInvalidClusterSize()
    {
        $tester = new CommandTester(new ClusterCommand());

        $this->setExpectedException('OutOfRangeException');

        $tester->execute(array(
            'record' => 8,
            '--cluster-size' => 5,
            '--cluster' => 7,
        ));
    }
}
