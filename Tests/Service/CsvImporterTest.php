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
     * @var CsvImporter
     */
    private $csvImporter;

    /**
     * @var MockObject
     */
    private $managerMock;

    /**
     * Initialise
     */
    public function setUp()
    {
        $filePath = $this->getCsvFilePath('working-import-file');
        $importCount = count(file($filePath));
        $this->managerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mapValidatorMock = $this
            ->getMockBuilder('Astina\Bundle\RedirectManagerBundle\Validator\MapValidator')
            ->disableOriginalConstructor()
            ->getMock();

        $mapValidatorMock
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(true));

        $this->csvImporter = new CsvImporter($this->managerMock, $mapValidatorMock);
    }

    /**
     * Test basic csv file import.
     */
    public function testCsvImport()
    {
        $filePath = $this->getCsvFilePath('working-import-file');
        $importCount = count(file($filePath));

        $this->managerMock->expects($this->exactly($importCount))->method('persist');
        $this->managerMock->expects($this->once())->method('flush');

        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');

        $result = $this->csvImporter->import($filePath, 301, true, $outputMock);

        $this->assertEquals($importCount, $result, 'Method returns wrong number of imported items.');
    }

    /**
     * @expectedException \Astina\Bundle\RedirectManagerBundle\Service\Exception\CsvImporterException
     */
    public function testIfExceptionIsRaisedWhenBadInputFileIsProvided()
    {
        $this->managerMock->expects($this->never())->method('persist');
        $this->managerMock->expects($this->never())->method('flush');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $this->csvImporter->import($this->getCsvFilePath('bad-data'), 301, true, $outputMock);
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
