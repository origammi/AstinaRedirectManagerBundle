<?php
namespace Astina\Bundle\RedirectManagerBundle\Service;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class CsvImporter
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Service
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
abstract class BaseService
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Returns Doctrine's entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEm()
    {
        return $this->doctrine->getManager('redirect');
    }
}
