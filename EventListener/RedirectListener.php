<?php

namespace Astina\Bundle\RedirectManagerBundle\EventListener;

use Astina\Bundle\RedirectManagerBundle\Entity\MapRepository;
use Astina\Bundle\RedirectManagerBundle\Entity\Map;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class RedirectListener
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
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

        /** @var $map Map */
        $map = $this->getMapRepository()->findOneBy(array('urlFrom' => $request->getPathInfo()));

        if (null == $map) {
            return;
        }

        $url = $map->getUrlTo();
        if ($baseUrl = $request->getBaseUrl()) {
            $url = $baseUrl . $url;
        }

        $response = new RedirectResponse($url, $map->getRedirectHttpCode());
        $event->setResponse($response);

        if ($map->isCountRedirects()) {
            $map->increaseCount();

            $em = $this->getEm();
            $em->persist($map);
            $em->flush();
        }
    }

    /**
     * Get repository for Map entity.
     *
     * @return MapRepository
     */
    private function getMapRepository()
    {
        return $this->doctrine->getRepository('AstinaRedirectManagerBundle:Map');
    }

    /**
     * Returns Doctrine's entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEm()
    {
        return $this->doctrine->getManager();
    }
}
