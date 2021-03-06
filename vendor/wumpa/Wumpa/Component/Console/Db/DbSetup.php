<?php

namespace Wumpa\Component\Console\Db;

use Wumpa\Component\FileSystem\File;
use Wumpa\Component\Database\Database;
use Wumpa\Component\Console\ComponentMom;
use Wumpa\Component\Renderer\Renderer;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class DbSetup extends ComponentMom {

	public function launch() {
		$this->clear();
		echo "\033[1mWumpa Database Setup started...\033[0m\n";
		echo "This will setup your database connection for you.\n";
		echo "\n";
		echo "You'll need these data:\n";
		echo "   Driver (mysql or pgsql)\n";
		echo "   Database name\n";
		echo "   Host\n";
		echo "   Port\n";
		echo "   User\n";
		echo "   Password\n";
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

		$data = array();
		echo "\n";

		do {
			$answer = readline("Driver of the database: [mysql/pgsql] ");
			if($answer != "mysql" && $answer != "pgsql") {
				echo "Driver must be mysql or pgsql.\n";
			}
		} while($answer != "mysql" && $answer != "pgsql");

		$data['driver'] = $answer;

		do {
			$answer = readline("Database's name: ");
			if($answer == "") {
				echo "Cannot be empty.\n";
			}
		} while($answer == "");

		$data['dbName'] = $answer;

		do {
			$answer = readline("Database's host: ");
			if($answer == "") {
				echo "Cannot be empty.\n";
			}
		} while($answer == "");

		$data['host'] = $answer;

		$data['port'] = readline("Database's port: ");

		do {
			$answer = readline("User: ");
			if($answer == "") {
				echo "Cannot be empty.\n";
			}
		} while($answer == "");

		$data['user'] = $answer;

		$data['password'] = readline("Password: ");


		$this->generateFile($data);
		echo "\n";
		echo "\033[32;1mDatabase configuration is now done!\033[0m\n";

		do {
			$start = strtolower(readline("Do you want to test database connectivity? [Y/n] "));
			if($start != "y" && $start != "yes" && $start != "n" && $start != "no") {
				echo "Invalid input.\n";
			}
		} while($start != "y" && $start != "yes" && $start != "n" && $start != "no");

		if($start == "n" || $start == "no") {
			try {
				$dbh = App::getDatabase()->connect();
			} catch(\Exception $e) {
				echo "\033[31;1mFAIL\033[0m\n\n";
				exit;
			}
			echo "\033[32;1mOK\033[0m\n";
		}

	}

	private function generateFile($data) {
		$renderer = new Renderer(__DIR__."/../FileTemplate");

		$file = new File($this->getProjectPath(). "config/database.php");
		$file->open();
		fwrite($resource = $file->getResource(), $renderer->render("database.php.twig", array("db" => $data)));

		$file->close();
	}

}
