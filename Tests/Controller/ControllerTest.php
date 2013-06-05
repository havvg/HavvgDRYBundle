<?php

namespace Havvg\Bundle\DRYBundle\Tests\Controller;

use Havvg\Bundle\DRYBundle\Tests\AbstractTest;

class ControllerTest extends AbstractTest
{
    protected function setUp()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped();
        }
    }

    public function testFrameworkControllerIsValid()
    {
        $this->assertInstanceOf('Symfony\Bundle\FrameworkBundle\Controller\Controller', $this->createController());
    }
}
