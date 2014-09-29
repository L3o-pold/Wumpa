<?php

namespace Pulsar\Component\Console\Model;

use Pulsar\Component\Console\ConsoleComponentMom;
use Pulsar\Component\Console\ConsoleComponentInterface;
use Pulsar\Component\Console\Db\DbSetup;
use Pulsar\Component\Database\Database;
use Pulsar\Component\Database\Analyzer\PgAnalyzer;

class ModelSetup extends ConsoleComponentMom implements ConsoleComponentInterface {
	
	const DB_ERROR = "db_error";
	const TABLE_NOT_FOUND = "table_not_found";
	const NO_MODEL_DIR = "no_model_dir";
	const TABLE_FOUND = "table_found";
	const DB_OK = "db_ok";
	const MODEL_DIR_OK = "model_dir_ok";

	public function launch() {
		$this->display();
		include_once 'Config/database.php';
		if(is_null($db_config)) {
			$this->errorMessage(self::DB_ERROR);
			$answer = $this->step();
			while($answer != "y" && $answer != "yes" && $answer != "n" && $answer != "no") {
				$this->inputError();
				$answer = $this->step();
			}
			if($answer == "n" || $answer == "no") {
				exit;
			} else {
				include_once 'Component/Console/Db/DbSetup.php';
				$controller = new DbSetup();
				$controller->launch();
			}
		}
		$this->successMessage(self::DB_OK);
		include_once 'Config/system.php';
		if(is_null($model_dir)) {
			$this->errorMessage(self::NO_MODEL_DIR);
			exit;
		}
		$this->successMessage(self::MODEL_DIR_OK);
		
		$this->nextStep();
		$this->display();
		include_once 'Component/Database/Database.php';
		$GLOBALS['db_config'] = $db_config;
		Database::init();
		if(Database::get()->getDriver() == "pgsql") {
			include_once 'Component/Database/Analyzer/AnalyzerInterface.php';
			include_once 'Component/Database/Analyzer/PgAnalyzer.php';
			$analyzer = new PgAnalyzer();
		} elseif (Database::get()->getDriver() == "mysql") {
			echo "cake";
		}
		Database::connect();
		do {
			$table = $this->step();
			if($table != "") {
				while(!in_array($table, $analyzer->getTables())) {
					$this->errorMessage(self::TABLE_NOT_FOUND);
					$table = $this->step();
				}
				$this->successMessage(self::TABLE_FOUND);
					
				$this->nextStep();
				$class = $this->step();
				echo $class;
				echo "\n";
				$this->previousStep();
			}
		} while($table != "");
		
	}
	
	public function step() {
		switch($this->getStep()) {
			case 0:
				return readline("Do you want to setup a database now? [Y/n] ");
			case 1:
				return readline("Enter a table name (case sensitive): (leave blank to exit) ");
			case 2:
				return readline("Enter the desired name for the generated class: ");
		}
	}
	
	public function display() {
		switch ($this->getStep()) {
			case 0:
				echo "Pulsar's Model Generator started...\n";
				echo "This will generate model class depending on your database structure.\n";
				echo "\n";
				echo "Requirements:\n";
				echo "  A database must be defined in database.php config file.\n";
				echo "  A model storing folder must be defined in system.php config file.\n";
				echo "\n";
				break;
			case 1:
				echo "\n";
				break;
		}
	}
	
	public function inputError() {
		echo "Invalid input\n";
	}
	
	public function errorMessage($type) {
		switch($type) {
			case self::DB_ERROR:
				echo "Database configuration: missing.\n";
				break;
			case self::NO_MODEL_DIR:
				echo "Model directory: missing";
				break;
			case self::TABLE_NOT_FOUND:
				echo "Table not found.\n";
				break;
		}
	}
	
	public function successMessage($type) {
		switch($type) {
			case self::DB_OK:
				echo "Database configuration: ok.\n";
				break;
			case self::MODEL_DIR_OK:
				echo "Model directory: ok.\n";
				break;
			case self::TABLE_FOUND:
				echo "Table found.\n";
				break;
		}
	}
}
