<?php

namespace Wumpa\Component\Console\Model;

use Wumpa\Component\App\ConsoleApp;
use Wumpa\Component\Console\Db\DbSetup;
use Wumpa\Component\Console\ComponentMom;
use Wumpa\Component\Database\Database;
use Wumpa\Component\Database\Analyzer\PgAnalyzer;
use Wumpa\Component\Renderer\Renderer;
use Wumpa\Component\FileSystem\File;

class ModelSetup extends ComponentMom {

	private $analyzer;

	public function launch() {
		$this->clear();
		echo "Wumpa Model Generator started...\n";
		echo "This tool will generate modele classes from your database structure.\n";
		echo "\n";
		echo "A configured database is required.\n";
		echo "\n";

		readline("Hit enter to continue...");

		if(is_null($this->getProjectPath())) {
			do {
				$projectPath = readline("\nEnter the path to the project you want to add a database: \n");
				if($projectPath == "") {
					echo "Path cannot be null.\n";
				}
			} while($projectPath == "");
			$this->setProjectPath($projectPath);
		}

		// NEED TO CHECK IF PATH END WITH "/"

		ConsoleApp::init($this->getProjectPath());
		ConsoleApp::run();

		echo "\nChecking if a database is set for the project... ";
		if(is_null(Database::get())) {
			echo "\033[31;1mFAIL\O33[0m\n";
			// => ENTER DBsetup
		} else {
			echo "\033[32;1mOK\033[0m\n";
		}

		switch(Database::get()->getDriver()) {
			case "pgsql":
				$this->setAnalyzer(new PgAnalyzer());
				break;
			default:
				echo "\nOnly PostgreSQL is supported for now.\n";
				exit;
		}

		echo "Checking database connectivity... ";
		$dbh = Database::connect();
		// If fail => Exception
		echo "\033[32;1mOK\033[0m\n";

		echo "\nRetrieving tables...\n";
		foreach($tables = $this->getAnalyzer()->getTables() as $table) {
			echo "  - ".$table."\n";
		}

		do {
			$selectedTable = readline("\nEnter the name of the table you want generate model from: (0 to exit)\n");
			if($selectedTable == "0")
				continue;
			if(!in_array($selectedTable, $tables)) {
				echo "This table does not exist.\n";
				continue;
			}

			echo "Table Found\n";
			$className = readline("Enter the name you want to give to the generated class:\n");
			echo "Generating class ".$className.".php...";
			$this->generateModel($selectedTable, $className);
			echo " \033[32;1mDone\033[0m\n";
		} while($selectedTable != "0");

		/*do {
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
		*/
	}

	private function generateModel($tableName, $className) {
		$renderer = new Renderer(__DIR__."/../FileTemplate");

		$data = array();
		$data["className"] = $className;
		$data["tableName"] = $tableName;
		$data["columns"] = $this->getAnalyzer()->getColumns($tableName);
		$data["primaries"] = $this->getAnalyzer()->getPrimaries($tableName);

		$model = new File(ConsoleApp::get(ConsoleApp::MODEL).$className.".php");
		$model->open();
		fwrite($model->getResource(), $renderer->render("Model.php.twig", $data));
		$model->close();
	}

	public function getAnalyzer() {
		return $this->analyzer;
	}

	public function setAnalyzer($analyzer) {
		$this->analyzer = $analyzer;
		return $this;
	}

}
