<?php

namespace Wumpa\Component\Console\Model;

use Wumpa\Component\App\App;
use Wumpa\Component\App\AppFactory;
use Wumpa\Component\Console\Db\DbSetup;
use Wumpa\Component\Console\ComponentMom;
use Wumpa\Component\Database\Analyzer\PgAnalyzer;
use Wumpa\Component\Renderer\Renderer;
use Wumpa\Component\FileSystem\File;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class ModelSetup extends ComponentMom {

	private $analyzer;

	public function launch() {
		$this->clear();
		echo "\033[1mWumpa Model Generator started...\033[0m\n";
		echo "This tool will generate model classes from your database structure.\n";
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

			if(substr($projectPath, strlen($projectPath)-1, 1) != "/")
				$projectPath .= "/";

			$this->setProjectPath($projectPath);
		}

		App::init($this->getProjectPath(), Appfactory::APP_TERM);

		echo "\nChecking if a database is set for the project... ";
		if(is_null(App::getDatabase())) {
			echo "\033[31;1mFAIL\O33[0m\n";

			do {
				$start = strtolower(readline("Do you want to setup a database now? [Y/n] "));
				if($start != "y" && $start != "yes" && $start != "n" && $start != "no") {
					echo "Invalid input.\n";
				}
			} while($start != "y" && $start != "yes" && $start != "n" && $start != "no");

			if($start == "n" || $start == "no") {
				echo "\n";
				exit;
			}

			$component = new DbSetup();
			$component->setProjectPath($this->getProjectPath());
			$component->launch();
		} else {
			echo "\033[32;1mOK\033[0m\n";
		}

		switch(App::getDatabase()->getDriver()) {
			case "pgsql":
				$this->setAnalyzer(new PgAnalyzer());
				break;
			default:
				echo "\nOnly PostgreSQL is supported for now. Sorry...\n";
				exit;
		}

		echo "Checking database connectivity... ";
		try {
			$dbh = App::getDatabase()->connect();
		} catch(\Exception $e) {
			echo "\033[31;1mFAIL\033[0m\n";

			do {
				$start = strtolower(readline("Do you want to setup a database now? [Y/n] "));
				if($start != "y" && $start != "yes" && $start != "n" && $start != "no") {
					echo "Invalid input.\n";
				}
			} while($start != "y" && $start != "yes" && $start != "n" && $start != "no");

			if($start == "n" || $start == "no") {
				echo "\n";
				exit;
			}

			$component = new DbSetup();
			$component->setProjectPath($this->getProjectPath());
			$component->launch();
		}

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

			echo "\033[32;1mTable Found\033[0m\n";
			$className = readline("Enter the name you want to give to the generated class:\n");
			echo "Generating class ".$className.".php...";
			$this->generateModel($selectedTable, $className);
			echo " \033[32;1mDone\033[0m\n";
		} while($selectedTable != "0");

	}

	private function generateModel($tableName, $className) {
		$renderer = new Renderer(__DIR__."/../FileTemplate");

		$data = array();
		$data["className"] = $className;
		$data["tableName"] = $tableName;
		$data["columns"] = $this->getAnalyzer()->getColumns($tableName);
		$data["primaries"] = $this->getAnalyzer()->getPrimaries($tableName);

		$model = new File(App::get()->getModelDir().$className.".php");
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
