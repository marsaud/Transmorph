<?php

require_once SRC_DIR . '/Transmorph/Plugin/Processor/IteratorNode.php';
require_once dirname(__FILE__) . '/AbstractTest.php';
require_once TEST_DOUBLES_PATH . '/TST_Transmorph_Processor.php';

/**
 * Test class for Transmorph_Plugin_Processor_IteratorNode.
 * Generated by PHPUnit on 2011-11-25 at 22:49:31.
 */
class Transmorph_Plugin_Processor_IteratorNodeTest extends Transmorph_Plugin_Processor_AbstractTest
{

    /**
     * @var Transmorph_Plugin_IteratorNode
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Transmorph_Plugin_Processor_IteratorNode();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers Transmorph_Plugin_Processor_IteratorNode::processMap
     * 
     * @dataProvider processMapDataProvider
     */
    public function testProcessMap($map)
    {
        $this->assertEquals($map, $this->object->processMap(new Transmorph_Processor(), $map));
    }

    public function processMapDataProvider()
    {
        $data = array();

        $data[0] = array(array(' >> '));
        $data[1] = array(array('/0 >> .a'));
        $data[2] = array(array('.a >> /0'));
        $data[3] = array(array('c(.a/0) >> /1.b'));

        $data[4] = array(array(
                ' >> ',
                '/0 >> .a',
                '.a >> /0',
                'c(.a/0) >> /1.b'
            ));

        return $data;
    }

    /**
     * @covers Transmorph_Plugin_Processor_IteratorNode::processMap
     * 
     * @dataProvider processMap2DataProvider
     *
     * @param type $expected
     * @param type $input
     * @param type $map 
     */
    public function testProcessMap2($expected, $input, $map)
    {
        require_once TEST_DOUBLES_PATH . '/TST_Transmorph_Processor.php';
        $t = new TST_Transmorph_Processor();
        $t->setInputForTest($input);
        $this->assertEquals($expected, $this->object->processMap($t, $map));
    }

    public function processMap2DataProvider()
    {
        $data = array();

        $data[0] = array(array('/1 >> ', '/2 >> '), array(1 => null, 2 => null), array('/# >> '));
        $data[1] = array(array('/1 >> /1', '/2 >> /2'), array(1 => null, 2 => null), array('/# >> /#'));
        $data[2] = array(array(
            '/0/0 >> /0.a', 
            '/0/1 >> /1.a',
            '/0/2 >> /2.a',
            '/1/0 >> /0.b', 
            '/1/1 >> /1.b',
            '/1/2 >> /2.b',
            ),
            array(
                0 => array('a', 'b', 'c'),
                1 => array('x', 'y', 'z')
            ),
            array(
                '/0/# >> /#.a',
                '/1/# >> /#.b',
            ));

        return $data;
    }

    /**
     * @covers Transmorph_Plugin_Processor_IteratorNode::processMap
     * 
     * @dataProvider processMapExceptionsDataProvider
     * 
     * @expectedException Transmorph_Exception
     */
    public function testProcessMapExceptions($map)
    {
        $this->object->processMap(new Transmorph_Processor(), $map);
    }

    public function processMapExceptionsDataProvider()
    {
        $data = array();

        $data[0] = array(array('/#/# >> '));
        $data[1] = array(array('.#.# >> '));
        $data[2] = array(array('/#.# >> '));
        $data[3] = array(array('.#/# >> '));
        $data[4] = array(array('/#/#/ >> '));
        $data[5] = array(array('/# >> /#/#'));
        $data[6] = array(array('/# >> .#.#'));
        $data[7] = array(array('/# >> .#/#.'));

        $data[8] = array(array(' >> /#'));
        $data[9] = array(array(' >> .#'));
        $data[10] = array(array(' >> /0/#'));
        $data[11] = array(array(' >> .a.#'));


        return $data;
    }
    
    /**
     * @covers Transmorph_Plugin_Processor_IteratorNode::processMap
     * 
     * @expectedException Transmorph_Exception
     * 
     * @dataProvider processMapExceptions2DataProvider
     *
     * @param type $input
     * @param type $map 
     */
    public function testProcessMapExceptions2($input, $map)
    {
        $t = new TST_Transmorph_Processor();
        $t->setInputForTest($input);
        $this->assertEquals($this->object->processMap($t, $map));
    }
    
    public function processMapExceptions2DataProvider()
    {
        $data = array();

        $data[0] = array('string', array('/# >> '));
        $data[1] = array(array(0 => 'string'), array('/0/# >> '));

        return $data;
    }
    
    /**
     * @covers Transmorph_Plugin_Processor_IteratorNode::getIterableNode
     * @covers Transmorph_Plugin_Processor_IteratorNode::_checkIterableNode
     */
    public function testGetIterableNode()
    {
        $this->assertEquals(array(0, 1), $this->object->getIterableNode(new Transmorph_Reader(), array(0, 1), ''));

        $this->setExpectedException('Transmorph_Exception');

        $this->object->getIterableNode(new Transmorph_Reader(), array(0, 1), '/0');
    }

    /**
     * @covers Transmorph_Plugin_Processor_IteratorNode::extendRule
     * @covers Transmorph_Plugin_Processor_IteratorNode::_checkIterableNode
     * 
     * @dataProvider extendRuleDataProvider
     *
     * @param type $expected
     * @param type $iterableNode
     * @param type $mapRule 
     */
    public function testExtendRule($expected, $iterableNode, $mapRule)
    {
        $this->assertEquals($expected, $this->object->extendRule($iterableNode, $mapRule));
    }

    public function extendRuleDataProvider()
    {
        $data = array();

        $data[0] = array(array('1', '2'), array(1 => null, 2 => null), '#');
        $data[1] = array(array('11', '22'), array(1 => null, 2 => null), '##');
        $data[2] = array(array('a', 'b'), array('a' => null, 'b' => null), '#');
        $data[3] = array(array('aa', 'bb'), array('a' => null, 'b' => null), '##');

        $object = new stdClass();
        $object->a = null;
        $object->b = null;

        $data[4] = array(array('a', 'b'), $object, '#');
        $data[5] = array(array('aa', 'bb'), $object, '##');

        $data[6] = array(array(), array(), '#');
        $data[7] = array(array(), new stdClass(), '#');

        $data[8] = array(array('X'), array(1 => null, 2 => null), 'X');

        return $data;
    }

    /**
     * @covers Transmorph_Plugin_Processor_IteratorNode::extendRule
     * @covers Transmorph_Plugin_Processor_IteratorNode::_checkIterableNode
     * 
     * @expectedException Transmorph_Exception
     */
    public function testExtendRuleException()
    {
        $this->object->extendRule('string', '#');
    }

}