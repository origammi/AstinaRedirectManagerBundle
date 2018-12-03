<?php

namespace Astina\Bundle\RedirectManagerBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class MapRepository
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Entity
 * @author    Philipp Kräutli <pkraeutli@astina.ch>
 * @copyright 2014 Astina AG (http://astina.ch)
 */
class MapRepository extends EntityRepository
{

    public function findAll()
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.group', 'g')
            ->orderBy('g.priority')
            ->addOrderBy('m.urlFrom')
            ->getQuery()
            ->getResult()
        ;
    }

    public function search($term = null, $page, $pageSize)
    {
        if ($page <= 0) {
            $page = 1;
        }

        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.group', 'g')
            ->addOrderBy('g.priority')
            ->addOrderBy('m.urlFrom');
        ;

        if ($pageSize) {
            $qb
                ->setFirstResult(($page - 1) * $pageSize)
                ->setMaxResults($pageSize);
        }

        if (null !== $term) {
            $qb
                ->where('m.urlFrom like :term')
                ->orWhere('m.urlTo like :term')
                ->orWhere('m.comment like :term')
                ->orWhere('g.name like :term')
                ->setParameter('term', '%' . $term . '%')
            ;
        }

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @param string $url
     * @param string $path
     * @param $excludeIds Optionally exclude records matching ids
     *
     * @return Map[]
     */
    public function findForUrlOrPath($url, $path, array $excludeIds = [])
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.urlFrom = :path')
            ->orWhere('m.urlFrom = :url')
            ->setParameter('path', $path)
            ->setParameter('url', $url)
            ->leftJoin('m.group', 'g')
            ->orderBy('g.priority')
            ->addOrderBy('m.urlFrom', 'desc'); // urls starting with "http" will be sorted before urls starting with "/"

        if (\count($excludeIds)) {
            $qb->andWhere('m.id NOT IN(:excludeIds)');
            $qb->setParameter('excludeIds', $excludeIds);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns entries that either match or that need further regex matching (host and/or path)
     *
     * @param $url
     * @param $path
     * @return Map[]
     */
    public function findCandidatesForUrlOrPath($url, $path)
    {
        $sql = "SELECT m.* FROM armb_redirect_map m
                WHERE m.urlFrom = :urlFrom 
                OR m.urlFrom = :path 
                OR (m.urlFromIsRegexPattern IS NOT NULL 
                AND m.urlFromIsRegexPattern <> 0)";

        $groups = $this->getGroups();

        $maps = $this->_em->getConnection()->fetchAll($sql, ['urlFrom' => $url, 'path' => $path]);

        $priority = [];
        $urlFrom = [];
        foreach ($maps as &$map) {
            $map['priority'] = $groups[$map['group_id']] ?? 0;
            $priority[] = $groups[$map['group_id']] ?? 0;
            $urlFrom[] = $map['urlFrom'];
        }

        \array_multisort($priority, SORT_ASC, SORT_NUMERIC, $urlFrom, SORT_DESC, SORT_NATURAL, $maps);

        $result = [];
        foreach ($maps as $map) {
            $result[] = $this->buildMapFromArray($map);
        }

        return $result;
    }

    /**
     * @param string $url
     * @param string $path
     *
     * @return Map
     */
    public function findOneForUrlOrPath($url, $path)
    {
        $maps = $this->findForUrlOrPath($url, $path);

        if (empty($maps)) {
            return null;
        }

        return current($maps);
    }

    /**
     * Get groups.
     *
     * @return array
     */
    private function getGroups()
    {
        $groups =  $this->_em->getConnection()->fetchAll('SELECT id, priority FROM armb_redirect_group');
        return  \array_reduce($groups, function ($result, $item) {
            $result[$item['id']] = $item['priority'];
            return $result;
        }, []);

    }

    /**
     * Build map from array.
     *
     * @param array $data
     * @return Map
     * @throws \Exception
     */
    public function buildMapFromArray(array $data)
    {
        $m = new Map();
        if (!empty($data['host'])) {
            $m->setHost($data['host']);
        }
        if (!empty($data['hostIsRegexPattern'])) {
            $m->setHostIsRegexPattern($data['hostIsRegexPattern']);
        }
        if (!empty($data['hostRegexPatternNegate'])) {
            $m->setHostRegexPatternNegate($data['hostRegexPatternNegate']);
        }
        if (!empty($data['urlFrom'])) {
            $m->setUrlFrom($data['urlFrom']);
        }
        if (!empty($data['urlFromIsRegexPattern'])) {
            $m->setUrlFromIsRegexPattern($data['urlFromIsRegexPattern']);
        }
        if (!empty($data['urlFromIsNoCase'])) {
            $m->setUrlFromIsNoCase($data['urlFromIsNoCase']);
        }
        if (!empty($data['urlTo'])) {
            $m->setUrlTo($data['urlTo']);
        }
        if (!empty($data['count'])) {
            $m->setCount($data['count']);
        }
        if (!empty($data['countRedirects'])) {
            $m->setCountRedirects($data['countRedirects']);
        }
        if (!empty($data['redirectHttpCode'])) {
            $m->setRedirectHttpCode($data['redirectHttpCode']);
        }

        return $m;
    }
}