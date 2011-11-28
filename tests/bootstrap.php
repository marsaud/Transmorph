<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author fabrice
 */

ini_set(
        'include_path', implode(PATH_SEPARATOR, array(
            ini_get('include_path'),
             realpath(dirname(__FILE__).'/../../../../../../Program Files/wamp/bin/php/php5.3.8/PEAR'),
        realpath(dirname(__FILE__).'/../../../../../../usr/share/php5/ZendFramework/ZendFramework-1.11.11-minimal/Transmorphrary')
        )));

$srcDir = realpath(dirname(__FILE__)).'/..';

require_once $srcDir . '/Transmorph/Exception.php';
require_once $srcDir . '/Transmorph/Processor.php';
require_once $srcDir . '/Transmorph/Reader/Exception.php';
require_once $srcDir . '/Transmorph/Reader.php';
require_once $srcDir . '/Transmorph/Writer.php';
require_once $srcDir . '/Transmorph/Writer/Exception.php';
require_once $srcDir . '/Transmorph/Rule/Exception.php';
require_once $srcDir . '/Transmorph/Rule.php';

require_once $srcDir . '/Transmorph/Plugin/Interface.php';
require_once $srcDir . '/Transmorph/Plugin/Abstract.php';
require_once $srcDir . '/Transmorph/Plugin/ClassCallback.php';

?>
