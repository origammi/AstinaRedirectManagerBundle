<?php
namespace Astina\Bundle\RedirectManagerBundle\Tests\EventListener;

use Astina\Bundle\RedirectManagerBundle\EventListener\RedirectListener;
use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class RedirectListenerTest
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Tests\EventListener
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
class RedirectListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This code would raise an error if would't be set properly.
     */
    public function testIfPostRequestAreSkipped()
    {
        $doctrineMock = $this
            ->getMockBuilder('Symfony\Bridge\Doctrine\RegistryInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $redirectListener = new RedirectListener($doctrineMock);

        $event = $this->getResponseEventMock('POST');

        $redirectListener->onKernelRequest($event);

        $this->assertTrue(true, "Method doesn't work properly on POST request.");
    }

    /**
     * Tests if method is working properly and if all mocks all called properly.
     */
    public function testIfListenerSetProperRedirect()
    {
        $map = new Map();
        $map
            ->setUrlFrom('/something')
            ->setUrlTo('/somewhere')
        ;

        $repoMock = $this
            ->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
            ->getMock()
        ;

        $repoMock
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($map))
        ;

        $managerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $managerMock
            ->expects($this->once())
            ->method('persist')
            ->with($map)
        ;

        $doctrineMock = $this
            ->getMockBuilder('Symfony\Bridge\Doctrine\RegistryInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $doctrineMock
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repoMock))
        ;

        $doctrineMock
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($managerMock))
        ;

        $redirectListener = new RedirectListener($doctrineMock);

        $eventMock = $this->getResponseEventMock('GET');

        $eventMock
            ->expects($this->once())
            ->method('setResponse')
        ;

        $eventMock
            ->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST))
        ;

        $redirectListener->onKernelRequest($eventMock);

        $this->assertEquals(1, $map->getCount(), 'Map count should be increased to 1.');
    }

    /**
     *
     * @param string $requestMethod
     *
     * @return \Symfony\Component\HttpKernel\Event\GetResponseEvent
     */
    private function getResponseEventMock($requestMethod)
    {
        $event = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->getRequestMock($requestMethod)))
        ;

        return $event;
    }

    /**
     *
     * @param string $method
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function getRequestMock($method)
    {
        $request = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue($method))
        ;

        return $request;
    }
}