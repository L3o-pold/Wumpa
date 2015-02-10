<?php

namespace Wumpa\Component\Console;

use Wumpa\Component\Console\Project\ProjectSetup;
use Wumpa\Component\Console\Db\DbSetup;
use Wumpa\Component\Console\Model\ModelSetup;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Console {

	const COMPO_NEW   = "new";
	const COMPO_DB    = "db";
	const COMPO_MODEL = "model";

	private $component;

	public function launch() {
		switch($this->getComponent()) {
			case self::COMPO_NEW:
				$component = new ProjectSetup();
				break;
			case self::COMPO_DB:
				$component = new DbSetup();
				break;
			case self::COMPO_MODEL;
				$component = new ModelSetup();
				break;
		}
		$component->launch();
	}

	public function displayArgsError($arg) {
		echo $arg." is not a valid command.\n";
		echo "Type -help to see a list of supported command.\n";
	}

	public function displayNoArgs() {
		echo "Type -help to see a list of supported command.\n";
	}

	public function displayHelp() {
		echo "Usage:\n";
		echo "   php console [option]\n";
		echo "\n";
		echo "   -help      Display this help\n";
		echo "   -new       Run project setup\n";
		echo "   -db        Run database setup\n";
		echo "   -model     Run model generator\n";
	}

	public function getComponent() {
		return $this->component;
	}

	public function setComponent($component) {
		$this->component = $component;
		return $this;
	}

}
