<?php

require_once '../include.php';

function double($i)
{
    return $i * 2;
}

function concat($a, $b)
{
    return $a . $b;
}

$input = new stdClass();

$input->words = array(
    0 => 'From',
    1 => 'Input',
    2 => 'Output'
    );

$input->numbers = array('single' => 1);

$t = new Transmorph_Processor();

$output = $t->run($input, './rules4');

var_dump($input);
echo PHP_EOL;
readfile('rules4');
echo PHP_EOL . PHP_EOL;
var_dump($output);