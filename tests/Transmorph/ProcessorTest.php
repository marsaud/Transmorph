<?php

require_once dirname(__FILE__) . '/../../Transmorph/Processor.php';

function callbackAddForTest($t1, $t2)
{
    return $t1 + $t2;
}

function callbackConcatForTest($s1, $s2)
{
    return $s1 . $s2;
}

function callbackNoParamForTest()
{
    return __FUNCTION__;
}

/**
 * Test class for Transmorph_Processor.
 * Generated by PHPUnit on 2011-10-17 at 19:57:18.
 */
class Transmorph_ProcessorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Transmorph_Processor
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Transmorph_Processor();
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
        $this->assertTrue($this->object instanceof Transmorph_Processor);
    }

    /**
     * @dataProvider findCallbackDataProvider
     */
    public function testFindCallback($mapEntry, $callback)
    {
        $result = $this->object->findCallback($mapEntry);
        $this->assertEquals($callback, $result);
        $this->assertInternalType('string', $result);
    }

    public function findCallbackDataProvider()
    {
        $data = array();

        $data[0] = array('testFonction1(x,y(),z(a,b),c(d(e)))', 'testFonction1');
        $data[1] = array('testFonction2($x,y(),z($a,$b),c(d($e)),\c)', 'testFonction2');
        $data[2] = array('testFonction3($x,$y,$z)', 'testFonction3');
        $data[3] = array('$x', '');
        $data[4] = array('\c', '');

        return $data;
    }

    /**
     * @dataProvider findCallbackParamsDataProvider
     */
    public function testFindCallbackParams($mapEntry, $callbackParams)
    {
        $this->assertEquals($callbackParams, $this->object->findCallbackParams($mapEntry));
    }

    public function findCallbackParamsDataProvider()
    {
        $data = array();

        $data[0] = array('t(x)', array('x'));
        $data[1] = array('t($x)', array('$x'));
        $data[2] = array('t(x,y)', array('x', 'y'));
        $data[3] = array('t($x,$y)', array('$x', '$y'));
        $data[4] = array('t(x,u(y))', array('x', 'u(y)'));
        $data[5] = array('t($x,u($y))', array('$x', 'u($y)'));
        $data[6] = array('t(u(x),v(y))', array('u(x)', 'v(y)'));
        $data[7] = array('t(u($x),v($y))', array('u($x)', 'v($y)'));
        $data[8] = array('t(u(x,y))', array('u(x,y)'));
        $data[9] = array('t(u(x,y),v(i,j))', array('u(x,y)', 'v(i,j)'));
        $data[10] = array('t(u($x,$y),v($i,$j))', array('u($x,$y)', 'v($i,$j)'));
        $data[11] = array('t(u($x,$y),v($i,$j),\c)', array('u($x,$y)', 'v($i,$j)', '\c'));
        $data[12] = array('t()', array());

        return $data;
    }

    /**
     * @dataProvider isConstDataProvider
     */
    public function testIsConst($mapEntry)
    {
        $this->assertTrue($this->object->isConst($mapEntry));
    }

    public function isConstDataProvider()
    {
        $data = array();

        $data[0] = array('\a');
        $data[1] = array('\1');
        $data[2] = array('\$0');
        $data[3] = array('\f(a,b)');

        return $data;
    }

    /**
     * @dataProvider isPathDataProvider
     */
    public function testIsPath($mapEntry)
    {
        $this->assertTrue($this->object->isPath($mapEntry));
    }

    /**
     * @dataProvider isPathFailsDataProvider
     *
     * @param string $mapEntry 
     */
    public function testIsPathFails($mapEntry)
    {
        $this->assertFalse($this->object->isPath($mapEntry));
    }

    public function isPathDataProvider()
    {
        $data = array();

        $data[0] = array('');
        $data[1] = array('/');
        $data[2] = array('.');
        $data[3] = array('/0');
        $data[4] = array('/0/');
        $data[5] = array('/0.');
        $data[6] = array('/1');
        $data[7] = array('/a');
        $data[8] = array('/a/');
        $data[9] = array('/a.');
        $data[10] = array('/1/2');
        $data[11] = array('/1/a');
        $data[12] = array('/1.a');
        $data[13] = array('/a/1');
        $data[14] = array('/a/b');
        $data[15] = array('/a.b');
        $data[16] = array('/a.b/');
        $data[17] = array('/a.b.');
        $data[18] = array('.a');
        $data[19] = array('.a/1');
        $data[20] = array('.a/1/');
        $data[21] = array('.a/1.');
        $data[22] = array('.a.b');

        return $data;
    }

    public function isPathFailsDataProvider()
    {
        $data = array();

        $data[0] = array('\a');
        $data[1] = array('\a/');
        $data[2] = array('\a.');
        $data[3] = array('a');
        $data[4] = array('.a\\');
        $data[5] = array('.a\.');
        $data[6] = array('.a\/');

        return $data;
    }

    /**
     * @dataProvider handleFileDataProvider
     *
     * @param type $lines
     * @param type $filePath 
     */
    public function testHandleFile($lines, $filePath)
    {
        $object = new Transmorph_Processor(array(), null);
        $this->assertEquals($lines, $object->handleFile($filePath));
    }

    public function handleFileDataProvider()
    {
        $data = array();

        $data[0] = array(array('string1', 'string2', 'string3withLF'), realpath(dirname(__FILE__) . '/../testResources/testHandleFile1'));
        $data[1] = array(array('string1', 'string2', 'string3withoutLF'), realpath(dirname(__FILE__) . '/../testResources/testHandleFile2'));
        $data[2] = array(array('beforeEmptyString', '', 'afterEmptyString'), realpath(dirname(__FILE__) . '/../testResources/testHandleFile3'));

        return $data;
    }

    /**
     * @dataProvider handleMapEntryDataProvider
     *
     * @param mixed $input
     * @param string $mapEntry
     * @param mixed $expected 
     */
    public function testHandleMapEntry($input, $mapEntry, $expected)
    {
        $this->assertEquals($expected, $this->object->handleMapEntry($input, $mapEntry));
    }

    /**
     * @dataProvider handleMapEnrtyExceptionsDataProvider
     * @expectedException Transmorph_Exception
     *
     * @param string $mapEntry 
     */
    public function testHandleMapEntryExceptions($mapEntry)
    {
        $this->object->handleMapEntry(null, $mapEntry);
    }

    public function handleMapEntryDataProvider()
    {
        $data = array();

        $data[0] = array(null, '\a', 'a');
        $data[1] = array(null, '\1', 1);
        $data[2] = array(null, '\0a', '0a');

        /**
         * @todo FEATURE : permettre de forcer le type des constantes.
         */
        $data[3] = array(null, '\true', 'true');
        $data[4] = array(null, '\false', 'false');

        $data[5] = array(array('a'), '', array('a'));
        $data[6] = array(array('a'), '/0', 'a');

        $data[7] = array(array(array('k' => 'a')), '', array(array('k' => 'a')));
        $data[8] = array(array(array('k' => 'a')), '/0', array('k' => 'a'));
        $data[9] = array(array(array('k' => 'a')), '/0/k', 'a');

        $input = new stdClass();
        $output = new stdClass();

        $input->m = 'a';
        $output->m = 'a';
        $data[10] = array($input, '', $output);
        $data[11] = array($input, '.m', 'a');

        $input = new stdClass();
        $output = new stdClass();

        $input->m = new stdClass();
        $input->m->n = 'a';
        $subOutput = new stdClass();
        $subOutput->n = 'a';
        $output->m = $subOutput;
        $data[12] = array($input, '', $output);
        $data[13] = array($input, '.m', $subOutput);
        $data[14] = array($input, '.m.n', 'a');

        $data[15] = array(null, 'callbackAddForTest(\1,\2)', 3);
        $data[16] = array(array(3, 4), 'callbackAddForTest(/0,/1)', 7);

        $input = new stdClass();
        $input->a = 1;
        $input->b = 2;

        $data[17] = array($input, 'callbackAddForTest(.a,.b)', 3);
        $data[18] = array($input, 'callbackConcatForTest(callbackAddForTest(.a,.b),callbackAddForTest(.b,.b))', '34');

        $data[19] = array(null, 'callbackNoParamForTest()', 'callbackNoParamForTest');

        return $data;
    }

    public function handleMapEnrtyExceptionsDataProvider()
    {
        $data = array();

        $data[0] = array('(\1)');
        $data[1] = array('(\1, \2)');
        $data[2] = array('(callbackAddForTest(\1,\2))');

        return $data;
    }

    /**
     * @dataProvider handleLineDataProvider
     *
     * @param mixed $input
     * @param string $line
     * @param mixed $expected 
     */
    public function testHandleLine($input, $line, $expected)
    {
        $output = null;

        $this->object->handleLine($output, $input, $line);
        $this->assertEquals($expected, $output);
    }

    public function handleLineDataProvider()
    {
        $data = array();

        $data[0] = array(null, '\1 >> ', 1);
        $data[1] = array(null, '\a >> ', 'a');
        $data[2] = array(null, '\a >> /0', array('a'));

        $output = new stdClass();
        $output->m = 'a';

        $data[3] = array(null, '\a >> .m', $output);
        $data[4] = array(array('a'), '/0 >> .m', $output);

        $input = new stdClass();
        $input->m = 'a';

        $data[5] = array($input, '.m >> .m', $output);
        $data[6] = array($input, ' >> ', $output); // Identité

        return $data;
    }

    /**
     * @dataProvider runDataProvider
     *
     * @param mixed $ouptut
     * @param mixed $input
     * @param string $filePath 
     */
    public function testRun($ouptut, $input, $filePath)
    {
        $this->assertEquals($ouptut, $this->object->run($input, $filePath));
    }

    public function runDataProvider()
    {
        $data = array();

        $data[0] = array('a', 'a', realpath(dirname(__FILE__) . '/../testResources/testRun0'));
        $data[1] = array('a', array(0 => 'a'), realpath(dirname(__FILE__) . '/../testResources/testRun1'));
        $data[2] = array('a', array('k' => 'a'), realpath(dirname(__FILE__) . '/../testResources/testRun2'));

        $object1 = new stdClass();
        $object1->m = 'a';

        $data[3] = array('a', $object1, realpath(dirname(__FILE__) . '/../testResources/testRun3'));
        $data[4] = array(array(0 => 'a'), 'a', realpath(dirname(__FILE__) . '/../testResources/testRun4'));
        $data[5] = array(array('k' => 'a'), 'a', realpath(dirname(__FILE__) . '/../testResources/testRun5'));
        $data[6] = array($object1, 'a', realpath(dirname(__FILE__) . '/../testResources/testRun6'));
        $data[7] = array(array(0 => 'a'), $object1, realpath(dirname(__FILE__) . '/../testResources/testRun7'));
        $data[8] = array($object1, array(0 => 'a'), realpath(dirname(__FILE__) . '/../testResources/testRun8'));

        $object2 = new stdClass();
        $object2->m1 = 111;
        $object2->m2 = 222;
        $object2->m3 = 333;
        $array2 = array(
            'k1' => 111,
            'k2' => 222,
            'k3' => 333
        );

        $data[9] = array($object2, $array2, realpath(dirname(__FILE__) . '/../testResources/testRun9'));
        $data[10] = array($array2, $object2, realpath(dirname(__FILE__) . '/../testResources/testRun10'));

        $object3 = new stdClass();
        $object3->m = new stdClass();
        $object3->n = new stdClass();
        $object3->m->x = 'mx';
        $object3->m->y = 'my';
        $object3->n->x = 'nx';
        $object3->n->y = 'ny';
        $array3 = array(
            'm' => array('x' => 'mx', 'y' => 'my'),
            'n' => array('x' => 'nx', 'y' => 'ny')
        );

        $data[11] = array($object3, $array3, realpath(dirname(__FILE__) . '/../testResources/testRun11'));
        $data[12] = array($array3, $object3, realpath(dirname(__FILE__) . '/../testResources/testRun12'));

        $data[13] = array(array(2, 3, 4), null, realpath(dirname(__FILE__) . '/../testResources/testRun13'));

        $arrayInput4 = array(
            'firstNames' => array('Albert', 'James', 'Mickey'),
            'lastNames' => array('Einstein', 'Bond', 'Mouse')
        );
        $name1 = new stdClass();
        $name1->first = 'Albert';
        $name1->last = 'Einstein';
        $name2 = new stdClass();
        $name2->first = 'James';
        $name2->last = 'Bond';
        $name3 = new stdClass();
        $name3->first = 'Mickey';
        $name3->last = 'Mouse';
        $arrayOutput4 = array($name1, $name2, $name3);

        $data[14] = array($arrayOutput4, $arrayInput4, realpath(dirname(__FILE__) . '/../testResources/testRun14'));
        $data[15] = array($arrayInput4, $arrayOutput4, realpath(dirname(__FILE__) . '/../testResources/testRun15'));

        return $data;
    }

}

?>
