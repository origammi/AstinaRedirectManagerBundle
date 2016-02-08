<?php
namespace Astina\Bundle\RedirectManagerBundle\Tests\EventListener;

use Astina\Bundle\RedirectManagerBundle\EventListener\RedirectListener;
use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Astina\Bundle\RedirectManagerBundle\Redirect\RedirectFinder;
use Symfony\Component\HttpFoundation\Request;
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
        $managerMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $redirectFinder = new RedirectFinder($managerMock);

        $redirectListener = new RedirectListener($redirectFinder);

        $event = $this->getResponseEventMock($this->getRequestMock('POST'));

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
            ->setUrlTo('/somewhere');

        $repoMock = $this
            ->getMockBuilder('Astina\Bundle\RedirectManagerBundle\Entity\MapRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repoMock
            ->expects($this->once())
            ->method('findCandidatesForUrlOrPath')
            ->will($this->returnValue(array($map)));

        $managerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $managerMock
            ->expects($this->once())
            ->method('persist')
            ->with($map);        

        $managerMock
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repoMock));

        $redirectFinder = new RedirectFinder($managerMock);

        $redirectListener = new RedirectListener($redirectFinder);

        $requestMock = $this->getRequestMock('GET', null, '/something');
        $eventMock = $this->getResponseEventMock($requestMock);

        $eventMock
            ->expects($this->once())
            ->method('setResponse');

        $redirectListener->onKernelRequest($eventMock);

        $this->assertEquals(1, $map->getCount(), 'Map count should be increased to 1.');
    }

    /**
     * Tests if baseUrl is removed from requestUri.
     *
     * @param string $baseUri
     * @param string $requestUri
     * @param string $result
     *
     * @dataProvider uriProvider
     */
    public function testIfPathIsProperlyCalculated($baseUri, $requestUri, $result)
    {
        $repoMock = $this
            ->getMockBuilder('Astina\Bundle\RedirectManagerBundle\Entity\MapRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repoMock
            ->expects($this->once())
            ->method('findCandidatesForUrlOrPath')
            ->with($this->anything(), $this->equalTo($result))
            ->will($this->returnValue(null));

        $managerMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $managerMock
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repoMock));

        $redirectFinder = new RedirectFinder($managerMock);

        $redirectListener = new RedirectListener($redirectFinder);

        $redirectListener->onKernelRequest($this->getResponseEventMock($this->getRequestMock('GET', $baseUri, $requestUri)));
    }

    /**
     * Provider for testIfPathIsProperlyCalculated.
     *
     * @return array
     */
    public function uriProvider()
    {
        return array(
            array('',                  '/?a-example-with-get-param=424',       '/?a-example-with-get-param=424'),
            array('/web/app_dev.php/', '/web/app_dev.php/something/something', '/something/something' ),
            array('/',                 '/url-path/',                           '/url-path/' )
        );
    }

    /**
     * @param Request $requestMock
     *
     * @return \Symfony\Component\HttpKernel\Event\GetResponseEvent
     */
    private function getResponseEventMock($requestMock = null)
    {
        if (! $requestMock) {
            $requestMock = $this->getRequestMock('GET');
        }

        $event = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($requestMock));

        $event
            ->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST));

        return $event;
    }

    /**
     * @param string $method
     * @param string $baseUrl
     * @param string $requestUri
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function getRequestMock($method, $baseUrl = null, $requestUri = null)
    {
        $request = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();


        $request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue($method));

        if ($baseUrl) {
            $request
                ->expects($this->any())
                ->method('getBaseUrl')
                ->will($this->returnValue($baseUrl));
        }

        if ($requestUri) {
            $request
                ->expects($this->any())
                ->method('getRequestUri')
                ->will($this->returnValue($requestUri));
        }

        return $request;
    }
}