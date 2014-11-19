<?php

namespace Astina\Bundle\RedirectManagerBundle\EventListener;

use Astina\Bundle\RedirectManagerBundle\Redirect\RedirectFinderInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
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
     * @var RedirectFinderInterface
     */
    private $redirectFinder;

    /**
     * @param RedirectFinderInterface $redirectFinder
     */
    public function __construct(RedirectFinderInterface $redirectFinder)
    {
        $this->redirectFinder = $redirectFinder;
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

        $response = $this->redirectFinder->findRedirect($request);

        if (null == $response) {
            return;
        }

        $event->setResponse($response);
    }
}
