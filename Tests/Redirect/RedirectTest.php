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
     * @param boolean $isRegex
     * @param boolean $negate
     * @param boolean $match
     *
     * @dataProvider hostProvider
     */
    public function testMatchesHost($hostPattern, $requestUrl, $isRegex, $negate, $match)
    {
        $redirect = $this->createRedirect($hostPattern, null, $requestUrl, $isRegex, $negate);

        $this->assertEquals($match, $redirect->matchesHost());
    }

    public function hostProvider()
    {
        return array(
            array('www.example.org', 'https://www.example.org/', false, false, true),
            array('^[^.]+\.example\.org$', 'http://www.example.org/', true, false, true),
            array('^[^.]+\.example\.org$', 'http://example.org/', true, false, false),
            array('^[^.]+\.example\.org$', 'http://example.org/', true, true, true),
            array(null, 'http://www.foo.bar', false, false, true)
        );
    }

    /**
     * @param string $urlFrom
     * @param string $requestUrl
     * @param boolean $isRegex
     * @param boolean $match
     *
     * @dataProvider pathProvider
     */
    public function testMatchesPath($urlFrom, $requestUrl, $isRegex, $match)
    {
        $redirect = $this->createRedirect(null, $urlFrom, $requestUrl, $isRegex, false, $match);

        $this->assertEquals($match, $redirect->matchesPath());
    }

    public function pathProvider()
    {
        return array(
            array('/piff', '/piff', false, true),
            array('^/foo/.+$', '/foo/123', true, true),
            array('^/foo/.+$', '/foo/', true, false),
            array('^/foo/(.+)$', '/foo/bar', true, true),
            array('^/foo/.+$', '/foo/123', false, false),
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
        $redirect = $this->createRedirect($hostPattern, null, $requestUrl, true, false, $urlTo);

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

    protected function createRedirect($hostPattern, $urlFrom, $requestUrl, $isRegex = false, $negate = false, $urlTo = null)
    {
        $request = Request::create($requestUrl);
        $map = new Map();
        $map->setUrlFrom($urlFrom);
        $map->setUrlFromIsRegexPattern($isRegex);
        $map->setUrlTo($urlTo);
        $map->setHost($hostPattern);
        $map->setHostIsRegexPattern($isRegex);
        $map->setHostRegexPatternNegate($negate);

        return new Redirect($request, $map);
    }
} 