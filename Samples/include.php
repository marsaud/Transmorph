<?php

$dir = realpath(dirname(__FILE__) . '/..');

require_once $dir . '/Transmorph/Exception.php';
require_once $dir . '/Transmorph/Reader/Exception.php';
require_once $dir . '/Transmorph/Writer/Exception.php';
require_once $dir . '/Transmorph/Rule/Exception.php';
require_once $dir . '/Transmorph/Exception.php';
require_once $dir . '/Transmorph/Exception.php';

require_once $dir . '/Transmorph/Plugin/StackInterface.php';
require_once $dir . '/Transmorph/Plugin/Stack.php';

require_once $dir . '/Transmorph/Processor.php';
require_once $dir . '/Transmorph/Reader.php';
require_once $dir . '/Transmorph/Writer.php';
require_once $dir . '/Transmorph/Rule.php';

require_once $dir . '/Transmorph/Plugin/Interface.php';
require_once $dir . '/Transmorph/Plugin/Processor/Interface.php';
require_once $dir . '/Transmorph/Plugin/Reader/Interface.php';
require_once $dir . '/Transmorph/Plugin/Writer/Interface.php';
require_once $dir . '/Transmorph/Plugin/Processor/Abstract.php';
require_once $dir . '/Transmorph/Plugin/Reader/Abstract.php';
require_once $dir . '/Transmorph/Plugin/Writer/Abstract.php';
require_once $dir . '/Transmorph/Plugin/Processor/ClassCallback.php';
require_once $dir . '/Transmorph/Plugin/Processor/IteratorNode.php';