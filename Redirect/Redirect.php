<?php

namespace Astina\Bundle\RedirectManagerBundle\Redirect;

use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Symfony\Component\HttpFoundation\Request;

class Redirect
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Map
     */
    private $map;

    private $patternMatches;

    function __construct(Request $request, Map $map)
    {
        $this->request = $request;
        $this->map = $map;
    }

    public function getRedirectUrl()
    {
        $redirectUrl =  $this->map->getUrlTo();

        if (!$this->isAbsoluteUrl($redirectUrl) && $baseUrl = $this->request->getBaseUrl()) {
            $redirectUrl = $baseUrl . $redirectUrl;
        }

        $redirectUrl = $this->applyReplacements($redirectUrl);

        return $redirectUrl;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function matchesRequest()
    {
        return $this->matchesHost() && $this->matchesPath();
    }

    public function matchesHost()
    {
        if (null == ($host = $this->map->getHost())) {
            return true;
        }

        if (!$this->map->getHostIsRegexPattern()) {
            return $host === $this->request->getHost();
        }

        return !$this->map->isHostRegexPatternNegate() == preg_match('#' . $host . '#', $this->request->getHost(), $this->patternMatches);
    }

    public function matchesPath()
    {
        if (!$this->map->getUrlFromIsRegexPattern()) {
            return $this->map->getUrlFrom() === $this->request->getRequestUri();
        }

        return preg_match('#' . $this->map->getUrlFrom() . '#', $this->request->getRequestUri(), $this->patternMatches);
    }

    protected function applyReplacements($redirectUrl)
    {
        if (null === $this->patternMatches) {
            $this->matchesHost();
        }

        if (!$this->patternMatches) {
            return $redirectUrl;
        }

        foreach ($this->patternMatches as $group => $match) {
            $redirectUrl = str_replace('$' . $group, $match, $redirectUrl);
        }

        $redirectUrl = preg_replace('/\$[0-9+]/', '', $redirectUrl);

        return $redirectUrl;
    }

    /**
     * @param string $url
     *
     * @return int
     */
    protected function isAbsoluteUrl($url)
    {
        return preg_match('/^https?:\/\//', $url);
    }
}
