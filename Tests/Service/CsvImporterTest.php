<?php
namespace Astina\Bundle\RedirectManagerBundle\Tests\Service;

use Astina\Bundle\RedirectManagerBundle\Service\CsvImporter;

/**
 * Class CsvImporterTest
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Tests\Service
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
class CsvImporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test basic csv file import.
     */
    public function testCsvImport()
    {
        $filePath = $this->getCsvFilePath('working-import-file');
        $importCount = count(file($filePath));

        $managerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $managerMock
            ->expects($this->exactly($importCount))
            ->method('persist');

        $managerMock
            ->expects($this->once())
            ->method('flush');

        $doctrineMock = $this
            ->getMockBuilder('Symfony\Bridge\Doctrine\RegistryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $doctrineMock
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($managerMock));

        $csvImporter = new CsvImporter($doctrineMock);
        $result = $csvImporter->import($filePath, 301, true);

        $this->assertEquals($importCount, $result, 'Method returns wrong number of imported items.');
    }

    /**
     * @expectedException \Astina\Bundle\RedirectManagerBundle\Service\Exception\CsvImporterException
     */
    public function testIfExceptionIsRaisedWhenBadInputFileIsProvided()
    {
        $managerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $doctrineMock = $this
            ->getMockBuilder('Symfony\Bridge\Doctrine\RegistryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $doctrineMock
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($managerMock));

        $csvImporter = new CsvImporter($doctrineMock);

        $csvImporter->import($this->getCsvFilePath('bad-data'), 301, true);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getCsvFilePath($name)
    {
        return dirname(__FILE__) . "/data/$name.csv";
    }
}
