<?php
namespace Astina\Bundle\RedirectManagerBundle\Tests\Entity;

use Astina\Bundle\RedirectManagerBundle\Entity\Map;

/**
 * Class MapTest
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Tests\Entity
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
class MapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int $redirectCode
     *
     * @dataProvider redirectCodeProvider
     */
    public function testIfMapAcceptsValidHttpRedirectCodes($redirectCode)
    {
        $map = new Map();
        $map->setRedirectHttpCode($redirectCode);

        $this->assertEquals($redirectCode, $map->getRedirectHttpCode(), 'Invalid redirect code returned.');
    }

    /**
     * @return array
     */
    public function redirectCodeProvider()
    {
        return array(
            array(300),
            array(301),
            array(302),
            array(303),
            array(304),
            array(305),
            array(306),
            array(307),
            array(308)
        );
    }

    /**
     * @expectedException \Exception
     * @dataProvider httpCodeProvider
     */
    public function testIfExceptionIsRaisedWhenWrongHttpCodeIsGiven($httpCode)
    {
        $map = new Map();
        $map->setRedirectHttpCode($httpCode);
    }

    /**
     * @return array
     */
    public function httpCodeProvider()
    {
        return array(
            array(100),
            array(200),
            array(400),
            array(404),
        );
    }
}
