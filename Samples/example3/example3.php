#!/usr/bin/php
<?php
$dir = dirname(__FILE__);
require_once $dir . '/../include.php';

$input = new stdClass();
$input->first = array(0 => 'zero', 1 => 'one');
$input->second = array(3 => 'three');

sampleRunner($input, $dir . '/rules3');