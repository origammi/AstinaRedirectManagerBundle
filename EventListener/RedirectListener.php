<?php

namespace Astina\Bundle\RedirectManagerBundle\EventListener;

use Astina\Bundle\RedirectManagerBundle\Entity\MapRepository;
use Astina\Bundle\RedirectManagerBundle\Entity\Map;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RedirectListener
 *
 * @package   Astina\Bundle\RedirectManagerBundle\EventListener
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @author    Philipp Kräutli <pkraeutli@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
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

        $path = str_replace($request->getBaseUrl(), '/', $request->getRequestUri());

        /** @var $map Map */
        $map = $this->getMapRepository()->findOneBy(array('urlFrom' => $path));

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
