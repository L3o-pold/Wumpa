<?php

namespace Wumpa\Component\Console\Db;

use Wumpa\Component\Console\ConsoleComponentInterface;
use Wumpa\Component\File\File;
use Wumpa\Component\Database\Database;

class DbSetup {

	public function launch($projectPath) {
		echo "\nWumpa Database Setup started...\n";
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

		
		$this->generateFile($projectPath, $data);
		echo "\n";
		echo "Database configuration is now done!\n";
		echo "\n";
	}
	
	private function generateFile($projectPath, $data) {
		$file = new File($projectPath . "/config/database.php");
		$file->open();
		$resource = $file->getResource();
		fwrite($resource, "<?php\n");
		fwrite($resource, "\n");
		fwrite($resource, "/**\n");
		fwrite($resource, " * This array define the Database(s).\n");
		fwrite($resource, " * If you don't use any database set to null..\n");
		fwrite($resource, " * By default this return null.\n");
		fwrite($resource, " */\n");
		fwrite($resource, "\n");
		fwrite($resource, "return array(\n");
	
		foreach ($data as $key => $val) {
			fwrite($resource, "\t'".$key."' => '".$val."',\n");
		}
	
		fwrite($resource, ");");
		$file->close();
	}

}
