<?php

require_once '../include.php';


$input = array(
    0 => 5,
    1 => 10,
    2 => 15
    );

$t = new Transmorph_Processor();

$output = $t->run($input, './rules6_a');

var_dump($input);
echo PHP_EOL;
readfile('rules6_a');
echo PHP_EOL . PHP_EOL;
var_dump($output);