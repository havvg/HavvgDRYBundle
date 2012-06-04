<?php

namespace Havvg\Bundle\DRYBundle\Tests\Controller\Extension;

use Havvg\Bundle\DRYBundle\Tests\AbstractTest;

class ExtensionTest extends AbstractTest
{
    public function testFrameworkControllerIsValid()
    {
        $this->assertInstanceOf('Symfony\Bundle\FrameworkBundle\Controller\Controller', $this->createController());
    }
}
