#!/usr/bin/php
<?php
$dir = dirname(__FILE__);
require_once $dir . '/../include.php';

$input = array(
    0 => 'zero',
    1 => 'one',
    'one' => 1,
    'two' => 2);

sampleRunner($input, $dir . '/rules1');