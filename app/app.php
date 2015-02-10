<?php

/**
 * This file is the entry point of the Wumpa Framework.
 * It's the first one to be called.
 *
 * This file can handle 2 types of includes: Classic and Ajax
 * Classic is used for normal server side execution from an index.php file
 * Ajax is used for ajax call. A variable named $call_dir must be defined before
 * requiring this file.
 */

use Wumpa\Component\App\App;
use Wumpa\Component\App\AppFactory;


include_once __DIR__."/../vendor/autoload.php";

$appType =  (!isset($indexDir)) ? (Appfactory::APP_INDEX) : (AppFactory::APP_AJAX);
$indexDir = (!isset($indexDir)) ? (dirname(debug_backtrace()[0]['file'])."/") : ($indexDir);

App::init($indexDir, $appType);
