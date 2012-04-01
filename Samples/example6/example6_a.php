#!/usr/bin/php
<?php
$dir = dirname(__FILE__);
require_once $dir . '/../include.php';

$input = array(
    0 => 5,
    1 => 10,
    2 => 15
);

sampleRunner($input, $dir . '/rules6_a');