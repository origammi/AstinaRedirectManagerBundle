<?php

namespace Astina\Bundle\RedirectManagerBundle\Service;

use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Astina\Bundle\RedirectManagerBundle\Entity\MapRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RedirectFinder implements RedirectFinderInterface
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function findRedirect(Request $request)
    {
        $path = str_replace($request->getBaseUrl(), '/', $request->getRequestUri());
        $url = $request->getSchemeAndHttpHost() . $request->getRequestUri();

        /** @var MapRepository $repo */
        $repo = $this->doctrine->getRepository('AstinaRedirectManagerBundle:Map');
        $map = $repo->findOneForUrlOrPath($url, $path);

        if (null === $map) {
            return null;
        }

        $redirectUrl = $map->getUrlTo();
        if (!$this->isAbsoluteUrl($redirectUrl) && $baseUrl = $request->getBaseUrl()) {
            $redirectUrl = $baseUrl . $redirectUrl;
        }

        if ($map->isCountRedirects()) {
            $map->increaseCount();
            $em = $this->doctrine->getManager();
            $em->persist($map);
            $em->flush($map);
        }

        return new RedirectResponse($redirectUrl, $map->getRedirectHttpCode());
    }

    protected function isAbsoluteUrl($url)
    {
        return preg_match('/^https?:\/\//', $url);
    }
}