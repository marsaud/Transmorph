<?php

require_once '../include.php';

$input = new stdClass();
$input->first = array(0 => 'zero', 1 => 'one');
$input->second = array(3 => 'three');

$t = new Transmorph_Processor();

$output = $t->run($input, './rules3');

var_dump($input);
echo PHP_EOL;
readfile('rules3');
echo PHP_EOL . PHP_EOL;
var_dump($output);