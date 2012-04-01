<?php

$src = realpath(dirname(__FILE__) . '/..');

require_once $src . '/Transmorph/Exception.php';
require_once $src . '/Transmorph/Reader/Exception.php';
require_once $src . '/Transmorph/Writer/Exception.php';
require_once $src . '/Transmorph/Rule/Exception.php';
require_once $src . '/Transmorph/Exception.php';
require_once $src . '/Transmorph/Exception.php';

require_once $src . '/Transmorph/Plugin/StackInterface.php';
require_once $src . '/Transmorph/Plugin/Stack.php';

require_once $src . '/Transmorph/Processor.php';
require_once $src . '/Transmorph/Reader.php';
require_once $src . '/Transmorph/Writer.php';
require_once $src . '/Transmorph/Rule.php';

require_once $src . '/Transmorph/Plugin/Interface.php';
require_once $src . '/Transmorph/Plugin/Processor/Interface.php';
require_once $src . '/Transmorph/Plugin/Reader/Interface.php';
require_once $src . '/Transmorph/Plugin/Writer/Interface.php';
require_once $src . '/Transmorph/Plugin/Processor/Abstract.php';
require_once $src . '/Transmorph/Plugin/Reader/Abstract.php';
require_once $src . '/Transmorph/Plugin/Writer/Abstract.php';
require_once $src . '/Transmorph/Plugin/Processor/ClassCallback.php';
require_once $src . '/Transmorph/Plugin/Processor/IteratorNode.php';

function sampleRunner($input, $ruleFile, $plugins = array())
{
    $t = new Transmorph_Processor();
    foreach ($plugins as $plugin)
    {
        $t->appendPlugin($plugin);
    }

    $output = $t->run($input, $ruleFile);

    var_dump($input);
    echo PHP_EOL;
    readfile($ruleFile);
    echo PHP_EOL . PHP_EOL;
    var_dump($output);
}