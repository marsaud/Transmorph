<?php

require_once SRC_DIR . '/Transmorph/Plugin/Processor/VariableParser.php';

/**
 * Test class for Transmorph_Plugin_Processor_VariableParser.
 * Generated by PHPUnit on 2012-01-19 at 21:50:30.
 */
class Transmorph_Plugin_Processor_VariableParserTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Transmorph_Plugin_Processor_VariableParser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @dataProvider processRuleDataProvider
     *
     * @param type $vars
     * @param type $ruleString
     * @param type $expectedReadRule
     * @param type $expectedWriteRule 
     */
    public function testProcessRule($vars, $ruleString, $expectedReadRule, $expectedWriteRule)
    {
        $object = new Transmorph_Plugin_Processor_VariableParser($vars);
        
        $rule = new Transmorph_Rule($ruleString);
        $object->processRule(new Transmorph_Processor(), $rule);
        
        $this->assertEquals($expectedWriteRule, $rule->writeRule);
        $this->assertEquals($expectedReadRule, $rule->readRule);
    }
    
    public function processRuleDataProvider()
    {
        $data = array();
        
        $data[0] = array(array(), '/a >> /b', '/a', '/b');
        $data[1] = array(array(), '\a >> /b', '\a', '/b');
        $data[2] = array(array(), '.a >> .b', '.a', '.b');
        $data[3] = array(array(), ' >> ', '', '');
        $data[4] = array(array('a' => 'x', 'b' => 'y'), '/@a >> /@b', '/x', '/y');
        $data[5] = array(array('a' => 'x'), '/@a >> /@b', '/x', '/@b');
        $data[6] = array(array('b' => 'y'), '.@a >> .@b', '.@a', '.y');
        $data[7] = array(array('a' => 'x', 'b' => 'y'), '/@a::@b() >> /@b::@a', '/x::y()', '/y::x');
        $data[8] = array(array('a' => 'x', 'b' => 'y'), '@a(@b,@a::@b()) >> /@b', 'x(y,x::y())', '/y');
        
        return $data;
    }

}
