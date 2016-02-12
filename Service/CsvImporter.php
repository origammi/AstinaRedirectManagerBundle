<?php
namespace Astina\Bundle\RedirectManagerBundle\Service;

use Symfony\Component\Console\Output\OutputInterface;
use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Astina\Bundle\RedirectManagerBundle\Service\Exception\CsvImporterException;

/**
 * Class CsvImporter
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Service
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
class CsvImporter extends BaseService
{
    /**
     * Imports urls from file and returns number of items imported.
     *
     * @param string $file
     * @param int    $redirectCode
     * @param bool   $countRedirects
     * @param OutputInterface $output
     *
     * @throws Exception\CsvImporterException
     * @return int
     */
    public function import($file, $redirectCode, $countRedirects, OutputInterface $output)
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
                    ->setUrlTo($urlTo)
                    ->setRedirectHttpCode($redirectCode)
                    ->setCountRedirects($countRedirects);

                if ($this->getValidator()->validate($map)) {
                    $em->persist($map);
                    $count++;
                } else {
                    $output->writeln('<error>Circular redirect detected for ' . $map->getUrlTo() . '</error>');
                }
            }
            fclose($handle);

            $em->flush();
        }

        return $count;
    }
}
