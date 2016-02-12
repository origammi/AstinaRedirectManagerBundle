<?php

namespace Astina\Bundle\RedirectManagerBundle\Validator;

use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Doctrine\ORM\EntityManagerInterface;

class MapValidator
{
    /**
     * @var MapRepository
     */
    private $mapRepository;

    /**
     * @param MapRepository $mapRepository
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->mapRepository = $entityManager->getRepository('Astina\Bundle\RedirectManagerBundle\Entity\Map');
    }

    /**
     * Check for circular redirects etc
     * @param Map $map
     * @return boolean
     */
    public function validate(Map $map)
    {
        if ($map->getUrlFrom() == $map->getUrlTo()) {
            return false;
        }
        $self = $this;
        $accumulator = [];
        if ($map->getId()) {
            $accumulator = [$map->getId() => $map->getId()];
        }
        $recurse = function(Map $map, Map $startPointMap) use ($self, &$recurse, &$accumulator) {
            $matchingToUrls = $self->mapRepository->findForUrlOrPath($map->getUrlTo(), $map->getUrlTo(), $accumulator);
            foreach ($matchingToUrls as $match) {
                $accumulator[$match->getId()] = $match->getId();
                if ($startPointMap->getUrlFrom() == $match->getUrlTo()) {
                    return false;
                } elseif ($match->getUrlFrom() == $map->getUrlTo()) {
                    return $recurse($match, $startPointMap);
                }
            }
            return true;
        };

        return $recurse($map, $map);
    }
}