#!/usr/bin/php
<?php
$dir = dirname(__FILE__);
require_once $dir . '/../include.php';

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

sampleRunner($input, $dir . '/rules4');