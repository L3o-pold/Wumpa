<?php

/**
 * This file is the entry point of the Wumpa Framework.
 * It's the first one to be called.
 */

use Wumpa\Component\App\App;

session_start();

$call_dir = dirname(debug_backtrace()[0]['file'])."/";

include_once __DIR__ . "/../vendor/autoload.php";

App::init($call_dir);
