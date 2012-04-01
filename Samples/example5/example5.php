#!/usr/bin/php
<?php
$dir = dirname(__FILE__);
require_once $dir . '/../include.php';

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

$plugins = array(
    new Transmorph_Plugin_Processor_ClassCallback()
);

sampleRunner($input, $dir . '/rules5', $plugins);