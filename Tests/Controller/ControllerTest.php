<?php

namespace Havvg\Bundle\DRYBundle\Tests\Controller;

use Havvg\Bundle\DRYBundle\Tests\AbstractTest;

class ControllerTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            self::markTestSkipped();
        }
    }

    public function testFrameworkControllerIsValid()
    {
        $this->assertInstanceOf('Symfony\Bundle\FrameworkBundle\Controller\Controller', $this->createController());
    }
}
