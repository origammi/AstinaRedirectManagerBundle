<?php
namespace Astina\RedirectManagerBundle\Tests\EventListener;

use Astina\RedirectManagerBundle\EventListener\RedirectListener;

class RedirectListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedirectListener
     */
    private $redirectListener;

    public function setUp()
    {
        $doctrine = $this
            ->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('getRepository', 'getManager'))
            ->getMock()
        ;

        $this->redirectListener = new RedirectListener($doctrine);
    }

    /**
     * This code would raise an error if would't be set properly.
     */
    public function testIfPostRequestAreSkipped()
    {
        $event = $this->getResponseEventMock('POST');

        $this->redirectListener->onKernelRequest($event);

        $this->assertTrue(true, "Method doesn't work properly on POST request.");
    }

    /**
     *
     * @param string $requestMethod
     *
     * @return Symfony\Component\HttpKernel\Event\GetResponseEvent
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
     * @return Symfony\Component\HttpFoundation\Request
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