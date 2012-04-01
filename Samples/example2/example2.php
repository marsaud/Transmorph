#!/usr/bin/php
<?php
$dir = dirname(__FILE__);
require_once $dir . '/../include.php';

$input = new stdClass();
$input->one = 1;
$input->two = 2;

sampleRunner($input, $dir . '/rules2');