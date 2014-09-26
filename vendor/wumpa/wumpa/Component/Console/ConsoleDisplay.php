<?php

namespace Wumpa\Component\Console;

class ConsoleDisplay {
	
	public static function displayArgsError($arg) {
		echo $arg." is not a valid command. Type -help to see a list of supported command.\n";
	}
	
	public static function displayNoArgs() {
		echo "Type -help to see a list of supported command.\n";
	}
	
	public static function displayHelp() {
		echo "Usage:\n";
		echo "   php console [option]\n";
		echo "\n";
		echo "   -help      Display this help\n";
		echo "   -new       Run project setup\n";
		echo "   -model     Run model generator\n";
	}
	
}