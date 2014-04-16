<?php

namespace Astina\Bundle\RedirectManagerBundle\Tests\EventListener;

use Astina\Bundle\RedirectManagerBundle\EventListener\SubDomainListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class SubDomainListenerTest
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Tests\EventListener
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2014 Astina AG (http://astina.ch)
 */
class SubDomainListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $httpHost
     *
     * @dataProvider getHttpHostsWithSubDomain
     */
    public function testIfListenerSetsRedirectIfDetectsSubDomain($httpHost)
    {
        $pathName    = 'home';
        $pathParams  = array('_locale' => 'de');
        $redirectUrl = 'http://redirect-to-here.com';

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request
            ->expects($this->once())
            ->method('getHttpHost')
            ->will($this->returnValue($httpHost));

        // works only for GET requests
        $request
            ->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        // works only for master requests
        $event
            ->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST));

        $event
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        // listener should call setResponse with created RedirectResponse
        $event
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse'));

        $router = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $router
            ->expects($this->once())
            ->method('generate')
            ->with($pathName, $pathParams, true)
            ->will($this->returnValue($redirectUrl));

        $listener = new SubDomainListener($router, $pathName, $pathParams);

        $listener->onKernelRequest($event);
    }

    /**
     * @return array
     */
    public function getHttpHostsWithSubDomain()
    {
        return array(
            array('subdomain.example.com'),
            array('multi.subdomain.example.com'),
            array('v.e.r.y.m.u.l.t.i.s.u.b.d.o.m.a.i.n.example.com'),
            array('subdomain.example-is.com'),
        );
    }
}
