<?php

namespace Astina\Bundle\RedirectManagerBundle\Tests\Redirect;

use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Astina\Bundle\RedirectManagerBundle\Redirect\Redirect;
use Symfony\Component\HttpFoundation\Request;

class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $hostPattern
     * @param string $requestUrl
     * @param string $match
     *
     * @dataProvider hostProvider
     */
    public function testMatchesHost($hostPattern, $requestUrl, $match)
    {
        $redirect = $this->createRedirect($hostPattern, $requestUrl);

        $this->assertEquals($match, $redirect->matchesHost());
    }

    public function hostProvider()
    {
        return array(
            array('www.example.org', 'https://www.example.org/', true),
            array('^[^.]+\.example\.org$', 'http://www.example.org/', true),
            array('^[^.]+\.example\.org$', 'http://example.org/', false),
            array('!^[^.]+\.example\.org$', 'http://example.org/', true),
            array(null, 'http://www.foo.bar', true)
        );
    }

    /**
     * @param string $hostPattern
     * @param string $requestUrl
     * @param string $urlTo
     * @param string $expectedRedirectUrl
     *
     * @dataProvider replacementsProvider
     */
    public function testReplacements($hostPattern, $requestUrl, $urlTo, $expectedRedirectUrl)
    {
        $redirect = $this->createRedirect($hostPattern, $requestUrl, $urlTo);

        $this->assertEquals($expectedRedirectUrl, $redirect->getRedirectUrl());
    }

    public function replacementsProvider()
    {
        return array(
            array('^(.+?)\.example\.org$', 'http://foo.example.org/', '/$1', '/foo'),
            array('^(.+?)\.example\.org$', 'http://foo.example.org/', '/bar', '/bar'),
            array('^example\.(.+?)$', 'http://example.de/', '/$1', '/de'),
        );
    }

    protected function createRedirect($hostPattern, $requestUrl, $urlTo = null)
    {
        $request = Request::create($requestUrl);
        $map = new Map();
        $map->setUrlTo($urlTo);
        $map->setHostPattern($hostPattern);

        return new Redirect($request, $map);
    }
} 