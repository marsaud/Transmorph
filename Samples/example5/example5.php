<?php

require_once '../include.php';

class Calc
{

    public function add($a, $b)
    {
        return $a + $b;
    }

    function multiply($a, $b)
    {
        return $a * $b;
    }

}

$input = array(
    'a' => 2,
    'b' => 3
);


$t = new Transmorph_Processor();
$t->appendPlugin(new Transmorph_Plugin_ClassCallback());

$output = $t->run($input, './rules5');

var_dump($input);
echo PHP_EOL;
readfile('rules5');
echo PHP_EOL . PHP_EOL;
var_dump($output);