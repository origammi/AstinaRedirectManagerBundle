<?php

namespace Astina\Bundle\RedirectManagerBundle\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

interface RedirectFinderInterface
{
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function findRedirect(Request $request);
} 