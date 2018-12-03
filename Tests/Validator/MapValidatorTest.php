<?php

namespace Astina\Bundle\RedirectManagerBundle\Tests\Validator;

use Astina\Bundle\RedirectManagerBundle\Validator\MapValidator;
use Astina\Bundle\RedirectManagerBundle\Entity\Map;

/**
 * Class MapValidatorTest
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Tests\Validator
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
class MapValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MapValidator
     */
    private $mapValidator;

    /**
     * @var MockObject
     */
    private $mapRepository;

    /**
     * Initialise
     */
    public function setUp()
    {
        $this->mapRepository = $this->getMockBuilder('Astina\Bundle\RedirectManagerBundle\Entity\MapRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $mockEm = $this->createMock('Doctrine\ORM\EntityManagerInterface');
        $mockEm->expects($this->any())->method('getRepository')->will($this->returnValue($this->mapRepository));
        $this->mapValidator = new MapValidator($mockEm);
    }

    /**
     * @dataProvider testValidateProvider
     * @param Map $map
     * @param array $repoMaps
     * @param boolean $expectation
     */
    public function testValidate(Map $map, $repoMaps, $expectation)
    {
        $mockContext = $this->getMockBuilder('stdClass')
            ->setMethods(['addViolationAt'])
            ->getMock();
        $mockContext->expects($this->any())->method('addViolationAt')->will($this->returnValue(true));
        $this->mapRepository->expects($this->any())->method('findForUrlOrPath')->will($this->returnValue($repoMaps));
        $this->assertEquals($expectation, $this->mapValidator->validate($map, $mockContext));
    }

    public function testValidateProvider()
    {
        $mockMap1 = $this->getMockBuilder('Astina\Bundle\RedirectManagerBundle\Entity\Map')
            ->disableOriginalConstructor()
            ->getMock();
        $mockMap2 = clone $mockMap1;
        $mockMap3 = clone $mockMap1;
        $mockMap4 = clone $mockMap1;
        $mockMap1->expects($this->any())->method('getUrlFrom')->will($this->returnValue('/map1'));
        $mockMap1->expects($this->any())->method('getUrlTo')->will($this->returnValue('/map2'));
        $mockMap1->expects($this->any())->method('getId')->will($this->returnValue(1));
        $mockMap2->expects($this->any())->method('getUrlFrom')->will($this->returnValue('/map1'));
        $mockMap2->expects($this->any())->method('getUrlTo')->will($this->returnValue('/map2'));
        $mockMap2->expects($this->any())->method('getId')->will($this->returnValue(2));
        $mockMap3->expects($this->any())->method('getUrlFrom')->will($this->returnValue('/map2'));
        $mockMap3->expects($this->any())->method('getUrlTo')->will($this->returnValue('/map1'));
        $mockMap3->expects($this->any())->method('getId')->will($this->returnValue(3));
        $mockMap4->expects($this->any())->method('getUrlFrom')->will($this->returnValue('/map3'));
        $mockMap4->expects($this->any())->method('getUrlTo')->will($this->returnValue('/map3'));
        $mockMap4->expects($this->any())->method('getId')->will($this->returnValue(4));

        return [
            [$mockMap1, [$mockMap2], true],
            [$mockMap1, [$mockMap3], false],
            [$mockMap4, [$mockMap1], false]
        ];
    }
}
