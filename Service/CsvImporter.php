<?php
namespace Astina\Bundle\RedirectManagerBundle\Service;

use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Astina\Bundle\RedirectManagerBundle\Service\Exception\CsvImporterException;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class CsvImporter
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Service
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
class CsvImporter
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
     * Imports urls from file and returns number of items imported.
     *
     * @param $file
     *
     * @throws CsvImporterException
     *
     * @return int
     */
    public function import($file)
    {
        $count = 0;
        if (($handle = fopen($file, 'r')) !== false) {
            $em = $this->getEm();

            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) != 2) {
                    throw new CsvImporterException(sprintf('There should be only 2 columns in csv file. Got %d columns.', count($data)));
                }

                $urlFrom = $data[0];
                $urlTo   = $data[1];

                $map = new Map();
                $map
                    ->setUrlFrom($urlFrom)
                    ->setUrlTo($urlTo);

                $em->persist($map);

                $count++;
            }
            fclose($handle);

            $em->flush();
        }

        return $count;
    }

    /**
     * Returns Doctrine's entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEm()
    {
        return $this->doctrine->getManager();
    }
}