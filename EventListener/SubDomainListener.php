<?php

namespace Astina\Bundle\RedirectManagerBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class SubDomainListener
 *
 * @package   Astina\Bundle\RedirectManagerBundle\EventListener
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2014 Astina AG (http://astina.ch)
 */
class SubDomainListener
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $pathName;

    /**
     * @var array
     */
    private $pathParams;

    /**
     * @var int
     */
    private $redirectCode;

    /**
     * @var array
     */
    private $ignoredSubdomains;

    /**
     * @param Router $router
     * @param string $domain
     * @param string $pathName
     * @param array  $pathParams
     * @param int    $redirectCode
     */
    public function __construct(Router $router, $domain, $pathName, array $pathParams, $redirectCode = 301, $ignoredSubdomains = [])
    {
        $this->router       = $router;
        $this->domain       = $domain;
        $this->pathName     = $pathName;
        $this->pathParams   = $pathParams;
        $this->redirectCode = $redirectCode;        
        $this->ignoredSubdomains = $ignoredSubdomains;
    }

    /**
     * Executes on kernel event request. Must be executed after RedirectListener.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->getMethod() != 'GET') {
            return;
        }

        $host      = $request->getHttpHost();
        $subDomain = $this->getSubDomain($host);

        if (! $subDomain) {
            return;
        }

        $redirectResponse = new RedirectResponse($this->getRedirectUrl(), $this->redirectCode);

        $event->setResponse($redirectResponse);
    }

    /**
     * Returns subDomain of host.
     *
     * @param string $host
     *
     * @return string | null
     */
    private function getSubDomain($host)
    {
        if (in_array($host, $this->ignoredSubdomains)) {
            return null;
        }
        $subDomain = str_ireplace($this->domain, '', $host);
        $subDomain = trim($subDomain, '.');

        if (! strlen($subDomain) || in_array($subDomain, $this->ignoredSubdomains)) {
            return null;
        }

        return $subDomain;
    }

    /**
     * @return string
     */
    private function getRedirectUrl()
    {
        return $this->router->generate($this->pathName, $this->pathParams, true);
    }
}
