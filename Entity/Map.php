<?php

namespace Astina\RedirectManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Astina\RedirectManagerBundle\Entity\Map
 */
class Map
{
    /**
     * @var integer $id
     */
    private $id;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
}
