<?php
namespace Astina\Bundle\RedirectManagerBundle\Service;

use Doctrine\ORM\EntityManager;
use Astina\Bundle\RedirectManagerBundle\Validator\MapValidator;

/**
 * Class BaseService
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Service
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
abstract class BaseService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MapValidator
     */
    private $mapValidator;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, MapValidator $mapValidator)
    {
        $this->entityManager = $entityManager;
        $this->mapValidator = $mapValidator;
    }

    /**
     * Returns Doctrine's entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEm()
    {
        return $this->entityManager;
    }

    /**
     * Returns Doctrine's entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getValidator()
    {
        return $this->mapValidator;
    }
}
