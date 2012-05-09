<?php

namespace Havvg\Bundle\CommonControllerBundle\Tests\Controller\Extension;

use Havvg\Bundle\CommonControllerBundle\Tests\AbstractTest;

use Havvg\Bundle\CommonControllerBundle\Controller\Extension\Security;

/**
 * @covers Havvg\Bundle\CommonControllerBundle\Controller\Extension\Security
 */
class SecurityTest extends AbstractTest
{
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
