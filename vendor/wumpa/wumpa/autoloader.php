<?php

use Pulsar\Component\Autoloader\Autoloader;

include_once __DIR__.'/Component/Autoloader/Autoloader.php';

$autoloader = new Autoloader(null, __DIR__."/..");
$autoloader->register();
