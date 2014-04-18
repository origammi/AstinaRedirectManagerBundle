<?php

namespace Astina\Bundle\RedirectManagerBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class MapRepository
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Entity
 * @author    Philipp Kräutli <pkraeutli@astina.ch>
 * @copyright 2014 Astina AG (http://astina.ch)
 */
class MapRepository extends EntityRepository
{
    /**
     * @param string $url
     * @param string $path
     *
     * @return Map
     */
    public function findOneForUrlOrPath($url, $path)
    {
        $maps = $this->createQueryBuilder('m')
            ->where('m.urlFrom = :path')
            ->orWhere('m.urlFrom = :url')
            ->setParameter('path', $path)
            ->setParameter('url', $url)
            ->orderBy('m.urlFrom', 'desc') // urls starting with "http" will be sorted before urls starting with "/"
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (empty($maps)) {
            return null;
        }

        return current($maps);
    }
}
