<?php

namespace Astina\Bundle\RedirectManagerBundle\Redirect;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RedirectFinderInterface
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Redirect
 * @author    Philipp Kräutli <pkraeutli@astina.ch>
 * @copyright 2014 Astina AG (http://astina.ch)
 */
interface RedirectFinderInterface
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function findRedirect(Request $request);
}
