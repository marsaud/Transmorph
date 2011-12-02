<?php

require_once '../include.php';

$input = new stdClass();
$input->one = 1;
$input->two = 2;

$t = new Transmorph_Processor();

$output = $t->run($input, './rules2');

var_dump($input);
echo PHP_EOL;
readfile('rules2');
echo PHP_EOL . PHP_EOL;
var_dump($output);