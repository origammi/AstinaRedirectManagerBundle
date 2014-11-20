<?php

namespace Astina\Bundle\RedirectManagerBundle\Entity;

/**
 * Astina\Bundle\RedirectManagerBundle\Entity\Map
 */
class Map
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Group
     */
    private $group;

    /**
     * @var string $urlFrom
     */
    private $urlFrom;

    /**
     * @var string $urlTo
     */
    private $urlTo;

    /**
     * @var integer $count
     */
    private $count = 0;

    /**
     * Whether to count redirects or not
     *
     * @var bool
     */
    private $countRedirects = true;

    /**
     * @var int
     */
    private $redirectHttpCode = 302;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;
    }

    /**
     * Set urlFrom
     *
     * @param string $urlFrom
     *
     * @return Map
     */
    public function setUrlFrom($urlFrom)
    {
        $this->urlFrom = $urlFrom;

        return $this;
    }

    /**
     * Get urlFrom
     *
     * @return string
     */
    public function getUrlFrom()
    {
        return $this->urlFrom;
    }

    /**
     * Set urlTo
     *
     * @param string $urlTo
     *
     * @return Map
     */
    public function setUrlTo($urlTo)
    {
        $this->urlTo = $urlTo;

        return $this;
    }

    /**
     * Get urlTo
     *
     * @return string
     */
    public function getUrlTo()
    {
        return $this->urlTo;
    }

    /**
     * Set count
     *
     * @param integer $count
     *
     * @return Map
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Increases count by one.
     *
     * @return Map
     */
    public function increaseCount()
    {
        $this->count++;

        return $this;
    }

    /**
     * @param boolean $countRedirects
     *
     * @return Map
     */
    public function setCountRedirects($countRedirects)
    {
        $this->countRedirects = $countRedirects;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCountRedirects()
    {
        return $this->countRedirects;
    }

    /**
     * @param int $redirectHttpCode
     *
     * @throws \Exception
     *
     * @return Map
     */
    public function setRedirectHttpCode($redirectHttpCode)
    {
        if (!preg_match('/^30[0-8]$/', $redirectHttpCode)) {
            throw new \Exception('Invalid redirect HTTP code');
        }
        $this->redirectHttpCode = $redirectHttpCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getRedirectHttpCode()
    {
        return $this->redirectHttpCode;
    }
}
