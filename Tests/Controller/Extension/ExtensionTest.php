<?php

namespace Havvg\Bundle\CommonControllerBundle\Tests\Controller\Extension;

use Havvg\Bundle\CommonControllerBundle\Tests\AbstractTest;

class ExtensionTest extends AbstractTest
{
    public function testFrameworkControllerIsValid()
    {
        $this->assertInstanceOf('Symfony\Bundle\FrameworkBundle\Controller\Controller', $this->createController());
    }
}
