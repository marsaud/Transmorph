<?php

require_once './include.php';

$input = array(
    0 => 'zero',
    1 => 'one',
    'one' => 1,
    'two' => 2);

$t = new Transmorph_Processor();

$output = $t->run($input, './rules1');

var_dump($input, $output);