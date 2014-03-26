<?php

namespace Astina\Bundle\RedirectManagerBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MapRepository
 *
 */
class MapRepository extends EntityRepository
{
    /**
     * @param $url
     * @param $path
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
            ->getResult()
        ;

        if (empty($maps)) {
            return null;
        }

        return current($maps);
    }
}
