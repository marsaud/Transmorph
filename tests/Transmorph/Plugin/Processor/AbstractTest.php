<?php

require_once SRC_DIR . '/Transmorph/Plugin/Processor/Abstract.php';

class Transmorph_Plugin_Processor_Concrete extends Transmorph_Plugin_Processor_Abstract
{
    
}

/**
 * 
 * Test class for Transmorph_Plugin_Processor_Abstract.
 */
class Transmorph_Plugin_Processor_AbstractTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Transmorph_Plugin_Processor_Abstract
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Transmorph_Plugin_Processor_Concrete();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers Transmorph_Plugin_Processor_Abstract::processCallback
     */
    public function testProcessCallback()
    {
        $this->assertEquals('test', $this->object->processCallback(new Transmorph_Processor(), 'test'));
    }

    /**
     * @covers Transmorph_Plugin_Processor_Abstract::processCallbackParams
     */
    public function testProcessCallbackParams()
    {
        $cParams = array('test', 'check');
        $this->assertEquals($cParams, $this->object->processCallbackParams(new Transmorph_Processor(), $cParams));
    }

    /**
     * @covers Transmorph_Plugin_Processor_Abstract::processRule
     */
    public function testProcessRule()
    {
        $line = new Transmorph_Rule(' >> ');
        $this->assertEquals($line, $this->object->processRule(new Transmorph_Processor(), $line));
    }

    /**
     * @covers Transmorph_Plugin_Processor_Abstract::processMap
     */
    public function testProcessMap()
    {
        $map = array('test', 'check');
        $this->assertEquals($map, $this->object->processMap(new Transmorph_Processor(), $map));
    }

}