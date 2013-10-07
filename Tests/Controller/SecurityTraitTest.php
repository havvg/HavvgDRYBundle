<?php

namespace Havvg\Bundle\DRYBundle\Tests\Controller;

use Havvg\Bundle\DRYBundle\Controller\SecurityTrait;
use Havvg\Bundle\DRYBundle\Tests\AbstractTest;

/**
 * @covers Havvg\Bundle\DRYBundle\Controller\SecurityTrait
 */
class SecurityTraitTest extends AbstractTest
{
    public static function setUpBeforeClass()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            self::markTestSkipped();
        }
    }

    public function testGetAclProvider()
    {
        $aclProvider = $this->getMockForAbstractClass('Symfony\Component\Security\Acl\Model\AclProviderInterface');
        $container = $this->getContainerMock(array('security.acl.provider' => $aclProvider));

        $controller = $this->createController();
        $controller->setContainer($container);

        $this->assertSame($aclProvider, $controller->getAclProvider());
    }

    public function testGetSecurityContext()
    {
        $context = $this->getMockForAbstractClass('Symfony\Component\Security\Core\SecurityContextInterface');
        $container = $this->getContainerMock(array('security.context' => $context));

        $controller = $this->createController();
        $controller->setContainer($container);

        $this->assertSame($context, $controller->getSecurityContext());
    }

    /**
     * @depends testGetSecurityContext
     */
    public function testIsGranted()
    {
        $context = $this->getMockForAbstractClass('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->once())
            ->method('isGranted')
            ->will($this->returnValue(true))
        ;

        $container = $this->getContainerMock(array('security.context' => $context));
        $controller = $this->createController();
        $controller->setContainer($container);

        $this->assertTrue($controller->isGranted('ROLE_USER'));
    }

    public function testDenyAccessIf()
    {
        $controller = $this->createController();

        $controller->denyAccessIf(false);

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException');
        $controller->denyAccessIf(true);
    }

    public function testDenyAccessUnless()
    {
        $controller = $this->createController();

        $controller->denyAccessUnless(true);

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException');
        $controller->denyAccessUnless(false);
    }
}