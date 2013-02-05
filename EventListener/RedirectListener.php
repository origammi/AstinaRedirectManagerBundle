<?php

namespace Astina\Bundle\RedirectManagerBundle\EventListener;

use Astina\Bundle\RedirectManagerBundle\Entity\MapRepository;
use Astina\Bundle\RedirectManagerBundle\Entity\Map;

use Symfony\Bridge\Doctrine\RegistryInterface;
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
        $request = $event->getRequest();

        if ($request->getMethod() != "GET") {
            return;
        }

        $map = $this->getMapRepository()->findOneBy(array('urlFrom' => $request->getRequestUri()));

        if ($map instanceof Map) {
            $map->increaseCount();

            $response = new RedirectResponse($map->getUrlTo());

            $event->setResponse($response);

            $this->getEm()->persist($map);
            $this->getEm()->flush();
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
