<?php

use Wumpa\Component\Console\ConsoleDisplay;
use Wumpa\Component\Console\Project\ProjectSetup;
use Wumpa\Component\Console\Db\DbSetup;
use Wumpa\Component\Console\Model\ModelSetup;

include_once __DIR__ . "/../vendor/autoload.php";

if(isset($argv[1])) {
	switch ($argv[1]) {
		case "-help":
			ConsoleDisplay::displayHelp();
			break;
		case "-new":
			$component = new ProjectSetup();
			$component->launch();
			break;
		case "-model":
			echo "Not implemented yet...";
			break;
		default:
			ConsoleDisplay::displayArgsError($argv[1]);
			break;
	}
} else {
	ConsoleDisplay::displayNoArgs();
}

echo "\n";
