<?php

namespace Wumpa\Component\Autoloader;

use Wumpa\Component\App\App;

/**
 * This class offers transparent autoloading for user definined class in projects.
 * 
 * @author Bastien de Luca
 */
class Autoloader {
	
	public function load($class_name) {
		$dirs = array(
			App::get(App::INDEX),
			App::get(App::CONTROLLER),
			App::get(App::MODEL),
			App::get(App::VIEW),
			App::get(App::TEMPLATES),
		);
		
		foreach($dirs as $dir) {
			if(file_exists($dir.$class_name . '.php')) {
				require_once($dir.$class_name . '.php');
				return;
			}
		}
	}
	
	public static function register() {
		spl_autoload_register(array(new self(), 'load'));
	}
	
	public static function unregister() {
		spl_autoload_unregister(array(new self(), 'load'));
	}
	
}