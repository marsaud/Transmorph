<?php

require_once SRC_DIR . '/Transmorph/Plugin/Writer/Abstract.php';

class Transmorph_Plugin_Writer_Concrete extends Transmorph_Plugin_Writer_Abstract
{
    
}

/**
 * Test class for Transmorph_Plugin_Writer_Abstract.
 * Generated by PHPUnit on 2011-12-20 at 23:46:32.
 */
class Transmorph_Plugin_Writer_AbstractTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Transmorph_Plugin_Writer_Abstract
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Transmorph_Plugin_Writer_Concrete;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testProcessRuleNode()
    {
        $this->assertEquals('test', $this->object->processRuleNode(new Transmorph_Writer(), 'test'));
    }

}
