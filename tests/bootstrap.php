<?php

define('SRC_DIR', realpath(dirname(__FILE__)).'/..');

ini_set('include_path', implode(PATH_SEPARATOR, array(
    '/usr/share/php',
    '/usr/share/php/ZendFramework-1.11.11-minimal/library',
    SRC_DIR,
    '.'
)));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Transmorph');

define('TEST_DOUBLES_PATH', realpath(dirname(__FILE__) . '/testDoubles'));
define('TEST_RESOURCES_PATH', realpath(dirname(__FILE__) . '/testResources'));