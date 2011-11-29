<?php

require_once dirname(__FILE__) . '/../../Transmorph/Rule.php';

/**
 * Test class for TransmorpLine.
 * Generated by PHPUnit on 2011-10-23 at 11:53:27.
 */
class Transmorph_RuleTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider constructorDataProvider
     *
     * @param string $source
     * @param string $target
     * @param string $rule 
     */
    public function testConstructor($source, $target, $rule)
    {
        $object = new Transmorph_Rule($rule);
        $this->assertEquals($source, $object->readRule);
        $this->assertEquals($target, $object->writeRule);
    }

    /**
     * @dataProvider constructorExceptionsDataProvider
     * @expectedException Transmorph_Rule_Exception
     *
     * @param string $rule 
     */
    public function testConstructorExceptions($rule)
    {
        new Transmorph_Rule($rule);
    }

    public function constructorDataProvider()
    {
        $data = array();

        $data[0] = array('a', 'b', 'a >> b');
        $data[1] = array('a a', 'b b', 'a a >> b b');
        $data[2] = array('a>>', '>>b', 'a>> >> >>b');
        $data[3] = array('a ', ' b', 'a  >>  b');
        $data[4] = array('', 'b', ' >> b');
        $data[5] = array('', '', ' >> ');
        $data[6] = array('a', '', 'a >> ');

        return $data;
    }

    public function constructorExceptionsDataProvider()
    {
        $data = array();

        $data[0] = array('a > b');
        $data[1] = array('a > > b');
        $data[2] = array('a>> b');
        $data[3] = array('a >>b');
        $data[4] = array('>> b');
        $data[5] = array('a >>');
        $data[6] = array('>>');
        $data[7] = array(' >>');
        $data[8] = array('>> ');
        
        return $data;
    }
    
    /**
     * @expectedException OutOfRangeException
     */
    public function testOutOfRange()
    {
        $object = new Transmorph_Rule('x >> x');
        $object->unexisting;
    }

}