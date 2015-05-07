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
	private $tables = array();

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
		echo "Please enter a class name for each table found in the database (leave blank to use table name):\n\n";
		$this->setTables(array());
		foreach($tables = $this->getAnalyzer()->getTables() as $table) {
			if(($class = readline("  ".$table.": ")) === '') {
				$this->addTable($table, $table);
			} else {
				$this->addTable($table, $class);
			}

		}

		echo "\n";

		foreach($this->getTables() as $table => $class) {
			echo "Generating class ".$class.".php from table ".$table."...";
			$this->generateModel($table, $class);
			echo " \033[32;1mDone\033[0m\n";
		}

		echo "\n\033[32;1mModel generation done\033[0m\n";

	}

	private function generateModel($tableName, $className) {
		$renderer = new Renderer(__DIR__."/../FileTemplate");

		$data = array();
		$data["className"] = $className;
		$data["tableName"] = $tableName;
		$data["columns"] = $this->getAnalyzer()->getCols($tableName);
		$data["primaries"] = $this->getAnalyzer()->getPK($tableName);
		$data["dependencies"] = $this->findDependencies($tableName);
		$data["compositions"] = $this->findCompositions($tableName);
		$model = new File(App::get()->getModelDir().$className.".php");
		$model->open();
		fwrite($model->getResource(), $renderer->render("Model.php.twig", $data));
		$model->close();
	}

	private function findCompositions($tableName) {
		$compositions = array();
		foreach($this->getAnalyzer()->getTables() as $table) {
			foreach($this->getAnalyzer()->getFK($table) as $fk => $targetTable) {
				if($targetTable === $tableName) {
					$compositions[$this->getTables()[$table]] = $fk;
				}
			}
		}
		return $compositions;
	}

	private function findDependencies($tableName) {
		$dependencies = array();
		foreach($this->getAnalyzer()->getFK($tableName) as $fk => $table) {
			$dependencies[$fk] = $this->getTables()[$table];
		}
		return $dependencies;
	}

	public function getAnalyzer() {
		return $this->analyzer;
	}

	public function setAnalyzer($analyzer) {
		$this->analyzer = $analyzer;
		return $this;
	}

	public function getTables() {
		return $this->tables;
	}

	public function setTables($tables) {
		$this->tables = $tables;
		return $this;
	}

	public function addTable($tableName, $className) {
		$this->tables[$tableName] = $className;
	}

}
