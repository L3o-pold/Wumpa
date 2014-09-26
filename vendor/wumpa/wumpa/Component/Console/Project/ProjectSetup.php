<?php

namespace Wumpa\Component\Console\Project;

use Wumpa\Component\Console\ConsoleComponentInterface;
use Wumpa\Component\Console\Db\DbSetup;
use Wumpa\Component\File\File;

class ProjectSetup {
	
	public function launch() {
		echo "Wumpa Model Generator started...\n";
		echo "This will generate a new project in desired directory.\n";
		echo "(relative paths are from the directory containing the Wumpa folder)\n";
		echo "\n";
		
		chdir("../../");
		
		do {
			$name = readline("Enter the name of the project:\n");
			if($name == "")
				echo "Project name cannot be empty.\n";
		} while($name == "");
		
		do {
			$path = readline("Enter the path (relative or absolute) to the new project:\n");
			if($path == "")
				echo "Project path cannot be empty.\n";
		} while($path == "");
		
		if(substr($path, strlen($path)-1, 1) == "/")
			$path = substr($path, 0, strlen($path)-1);
		
		$projectPath = $path."/".$name;
		
		echo "\nCreating ".$projectPath." Directory... ";
		mkdir($projectPath);
		echo "Done.\n";
		
		echo "Generating project structure... ";
		$this->generateStructure($projectPath);
		echo "Done.\n";
		
		echo "Generating config files... ";
		$this->generateFiles($projectPath);
		echo "Done.\n";
		
		echo "Project generation is done.\n";
		echo "\n";
		
		readline("Hit enter to continue...");
		echo "\n";
		
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
		$component->launch($projectPath);
	}
	
	private function generateStructure($projectPath) {
		mkdir($projectPath."/config");
		mkdir($projectPath."/controller");
		mkdir($projectPath."/model");
		mkdir($projectPath."/view");
		mkdir($projectPath."/view/templates");
	}
	
	private function generateFiles($projectPath) {
		//INDEX
		$index = new File($projectPath."/index.php");
		$index->open();
		fwrite($index->getResource(), 
"<?php
				
use Wumpa\Component\App\App;
				
include_once '". realpath("Wumpa/app/app.php") ."';
		
App::run();");
		$index->close();
		
		//HTACCESS
		$htaccess = new File($projectPath."/.htaccess");
		$htaccess->open();
		fwrite($htaccess->getResource(),
"Options -Indexes
SetEnv SESSION_USE_TRANS_SID 0
Options +FollowSymLinks
IndexIgnore */*
# Turn on the RewriteEngine
RewriteEngine On
#  Rules
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php");
		$htaccess->close();
		
		//DATABASE CONFIG
		$db = new File($projectPath."/config/database.php");
		$db->open();
		fwrite($db->getResource(), 
"<?php

/**
 * This array define the Database(s).
 * If you don't use any database set to null.
 * By default this return null.
 */

return null;");
		$db->close();
		
		//ROUTES CONFIG
		$routes = new File($projectPath."/config/routes.php");
		$routes->open();
		fwrite($routes->getResource(),
"<?php

/**
 * This file contains all the routes used by your application.
 */

return array(
	\"Default\" => array(
		\"path\" => \"/\",
		\"controller\" => \"Home:default\",
	),
);");
		$routes->close();
		
		//SYSTEM CONFIG
		$system = new File($projectPath."/config/system.php");
		$system->open();
		fwrite($system->getResource(), 
"<?php

/**
 * Define the execution environement of the project.
 */

return array(
	// Enable/disable the wumpa exception handler
	'wumpa_exception_handler' => true,
	// Enable/disable debug_trace [true/false]
	'debug_trace' => true,
	// The file containing connection to database data
	'database_file' => 'database.php',
	// The file containing the routes of the project
	'routes_file' => 'routes.php',
	// The directory of your controllers
	'controller_dir' => 'controller/',
	// The directory of your models
	'model_dir' => 'model/',
	// The directory of the view
	'view_dir' => 'view/',
	// The directory of your templates (used for Twig)
	'templates_dir' => 'view/templates/',
);");
		$system->close();
		
		//EXEMPLE CONTROLLER
		$controller = new File($projectPath."/controller/HomeController.php");
		$controller->open();
		fwrite($controller->getResource(),
"<?php

use Wumpa\Component\Controller\Controller;

class HomeController extends Controller {

	public function defaultAction() {
		\$data = array(\"display\" => \"Hello World\");
		\$this->render(\"defaultTemplate.html.twig\", \$data);
	}

}");
		$controller->close();
		
		//EXEMPLE TEMPLATE
		$template = new File($projectPath."/view/templates/defaultTemplate.html.twig");
		$template->open();
		fwrite($template->getResource(),
"<!DOCTYPE html>

<html lang=\"en\">

<head>
<meta charset=\"utf-8\">
<title>Default Exemple</title>
</head>

<body>

<h1>{{ display }}</h1>

<p>
This page is a basic exemple.<br/>
This page is generated using Twig and Wumpa.
</p>

</body>

</html>");
		$template->close();
		
	}
	
}