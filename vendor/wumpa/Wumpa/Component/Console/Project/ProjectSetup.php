<?php

namespace Wumpa\Component\Console\Project;

use Wumpa\Component\Console\Db\DbSetup;
use Wumpa\Component\FileSystem\File;
use Wumpa\Component\Console\ComponentMom;
use Wumpa\Component\Renderer\Renderer;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class ProjectSetup extends ComponentMom {

	public function launch() {
		$this->clear();
		echo "\033[1mWumpa Model Generator started...\033[0m\n";
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

		if(substr($path, strlen($path)-1, 1) != "/")
			$path .= "/";

		$this->setProjectPath($path.$name."/");


		echo "\nCreating ".$this->getProjectPath()." Directory... ";
		mkdir($this->getProjectPath());
		echo "\033[32;1mDone.\033[0m\n";

		echo "Generating project structure... ";
		$this->generateStructure();
		echo "\033[32;1mDone.\033[0m\n";

		echo "Generating config files... ";
		$this->generateFiles();
		echo "\033[32;1mDone.\033[0m\n";

		echo "\033[32;1mProject generation is done.\033[0m\n";
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
		$component->setProjectPath($this->getProjectPath());
		$component->launch();
	}

	private function generateStructure() {
		mkdir($this->projectPath."config");
		mkdir($this->projectPath."controller");
		mkdir($this->projectPath."model");
		mkdir($this->projectPath."view");
		mkdir($this->projectPath."view/templates");
	}

	private function generateFiles() {
		$renderer = new Renderer(__DIR__."/../FileTemplate");
		//INDEX
		$index = new File($this->getProjectPath()."index.php");
		$index->open();
		fwrite($index->getResource(), $renderer->render("index.php.twig", array("wumpaPath" => realpath("Wumpa/app/app.php"))));
		$index->close();

		//HTACCESS
		$htaccess = new File($this->getProjectPath().".htaccess");
		$htaccess->open();
		fwrite($htaccess->getResource(), $renderer->render("htaccess.txt.twig", array()));
		$htaccess->close();

		//DATABASE CONFIG
		$db = new File($this->getProjectPath()."config/database.php");
		$db->open();
		fwrite($db->getResource(),$renderer->render("database.php.twig", array()));
		$db->close();

		//ROUTES CONFIG
		$routes = new File($this->getProjectPath()."config/routes.php");
		$routes->open();
		fwrite($routes->getResource(), $renderer->render("routes.php.twig", array()));
		$routes->close();

		//SYSTEM CONFIG
		$system = new File($this->getProjectPath()."config/system.php");
		$system->open();
		fwrite($system->getResource(), $renderer->render("system.php.twig", array()));
		$system->close();

		//EXEMPLE CONTROLLER
		$controller = new File($this->getProjectPath()."controller/HomeController.php");
		$controller->open();
		fwrite($controller->getResource(), $renderer->render("HomeController.php.twig", array()));
		$controller->close();

		//EXEMPLE TEMPLATE
		$template = new File($this->getProjectPath()."view/templates/defaultTemplate.html.twig");
		$template->open();
		fwrite($template->getResource(), $renderer->render("defaultTemplate.html.twig", array()));
		$template->close();

	}

}
