<?php

require_once dirname(__FILE__) . '/../../Transmorph/Processor.php';
require_once dirname(__FILE__) . '/../../Transmorph/Reader.php';

/**
 * Test class for Transmorph_Reader.
 * Generated by PHPUnit on 2011-10-23 at 11:53:36.
 */
class Transmorph_ReaderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Transmorph_Reader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Transmorph_Reader();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testConstructor()
    {
        $this->assertTrue($this->object instanceof Transmorph_Reader);
    }

    /**
     * @dataProvider queryDataProvider
     */
    public function testQuery($input, $path, $result)
    {
        $this->assertEquals($result, $this->object->query($input, $path));
    }

    /**
     * @dataProvider queryExceptionsDataProvider
     * @expectedException Transmorph_Reader_Exception
     */
    public function testQueryExceptions($input, $path)
    {
        $this->object->query($input, $path);
    }

    public function queryDataProvider()
    {
        $data = array();

        $data[0] = array('test', '', 'test');
        $data[1] = array('test', '/', 'test');
        $data[2] = array('test', '.', 'test');
        
        $data[3] = array('0test', '', '0test');
        $data[4] = array('01', '', '01');
        $data[5] = array(1, '', 1);

        $data[6] = array(array('test'), '', array('test'));
        $data[7] = array(array('test'), '/0', 'test');
        $data[8] = array(array('test0', 'test1'), '/1', 'test1');

        $data[9] = array(array('t' => 'test'), '', array('t' => 'test'));
        
        $data[10] = array(array(
                't1' => 'test1',
                't2' => 'test2'
            ), '/t2', 'test2');

        $input = new stdClass();
        $input->t1 = 'test1';
        $input->t2 = 'test2';
        $output = new stdClass();
        $output->t1 = 'test1';
        $output->t2 = 'test2';

        $data[11] = array($input, '', $output);
        $data[12] = array($input, '.t1', 'test1');
        $data[13] = array($input, '.t2', 'test2');

        $input = new stdClass();
        $input->node = new stdClass();
        $input->node->t = 'test';

        $node = new stdClass();
        $node->t = 'test';
        $output = new stdClass();
        $output->node = $node;

        $data[14] = array($input, '', $output);
        $data[15] = array($input, '.node', $node);
        $data[16] = array($input, '.node.t', 'test');

        $node = new stdClass();
        $node->t = 'test';

        $data[17] = array(array('k' => $node), '', array('k' => $node));
        $data[18] = array(array('k' => $node), '/k', $node);
        $data[19] = array(array('k' => $node), '/k.t', 'test');

        $input = new stdClass();
        $input->t = array('k' => 'test');
        $output = new stdClass();
        $output->t = array('k' => 'test');

        $data[20] = array($input, '', $output);
        $data[21] = array($input, '.t', array('k' => 'test'));
        $data[22] = array($input, '.t/k', 'test');

        return $data;
    }

    public function queryExceptionsDataProvider()
    {
        $data = array();

        $data[0] = array(array('test'), '/1');
        $data[1] = array(array('test0', 'test1'), '/2');

        $data[2] = array(array(
                't1' => 'test1',
                't2' => 'test2'
            ), '/t3');

        $input = new stdClass();
        $input->t1 = 'test1';
        $input->t2 = 'test2';

        $data[3] = array($input, '.t3');

        $input = new stdClass();
        $input->node = new stdClass();
        $input->node->t = 'test';

        $data[4] = array($input, '.x');
        $data[5] = array($input, '.node.x');

        $node = new stdClass();
        $node->t = 'test';

        $data[6] = array(array('k' => $node), '/x');
        $data[7] = array(array('k' => $node), '/k.x');

        $input = new stdClass();
        $input->t = array('k' => 'test');

        $data[8] = array($input, '.x');
        $data[9] = array($input, '.t/x');
        
        $data[10] = array(null, 'x');

        return $data;
    }

}