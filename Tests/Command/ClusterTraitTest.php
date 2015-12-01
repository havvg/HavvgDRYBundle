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
    public static function setUpBeforeClass()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            self::markTestSkipped();
        }
    }

    public function clusterProvider()
    {
        return [
            // No actual clustering, single processing.
            [1, 1, 1, 0],
            [1, 1, 5, 0],
            [1, 1, 17, 0],
            [1, 1, 42, 0],
            [1, 1, 1337, 0],

            // Two members in the cluster.
            [2, 1, 1, 1],
            [2, 1, 2, 0],
            [2, 2, 1, 0],
            [2, 2, 2, 1],

            // Three members in the cluster.
            [3, 1, 1, 1],
            [3, 1, 2, 1],
            [3, 1, 3, 0],
            [3, 2, 1, 0],
            [3, 2, 2, 1],
            [3, 2, 3, 1],
            [3, 3, 1, 1],
            [3, 3, 2, 0],
            [3, 3, 3, 1],
        ];
    }

    /**
     * @dataProvider clusterProvider
     */
    public function testCluster($size, $active, $record, $expected)
    {
        $tester = new CommandTester(new ClusterCommand());
        $exit = $tester->execute([
            'record' => $record,
            '--cluster-size' => $size,
            '--cluster' => $active,
        ]);

        self::assertEquals($expected, $exit);
    }

    public function testNoCluster()
    {
        $tester = new CommandTester(new ClusterCommand());
        $exit = $tester->execute([
            'record' => 5,
        ]);

        self::assertEquals(0, $exit);
    }

    public function testInvalidCluster()
    {
        $tester = new CommandTester(new ClusterCommand());

        $this->setExpectedException('InvalidArgumentException');

        $tester->execute([
            'record' => 8,
            '--cluster-size' => 5,
            '--cluster' => 0,
        ]);
    }

    public function testInvalidClusterSize()
    {
        $tester = new CommandTester(new ClusterCommand());

        $this->setExpectedException('OutOfRangeException');

        $tester->execute([
            'record' => 8,
            '--cluster-size' => 5,
            '--cluster' => 7,
        ]);
    }
}
