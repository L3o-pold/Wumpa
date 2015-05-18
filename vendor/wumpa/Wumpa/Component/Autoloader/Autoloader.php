<?php

namespace Wumpa\Component\Autoloader;

/**
 * Provide a transparent class autoloader for user defined class in its
 * project.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Autoloader {

	private $app;

	public function load($class_name) {
		$dir = $this->getApp()->getIndexDir();
		self::explore($dir, $class_name);
	}

	private static function explore($dir, &$class_name) {
		$dir .= (substr($dir, strlen($dir)-1, 1) === '/') ? '' : '/';

		foreach (scandir($dir) as $subDir) {
			if(is_dir($dir.$subDir) && $subDir !== "." && $subDir !== "..")
				self::explore($dir.$subDir, $class_name);
		}

		if(file_exists($dir.$class_name.'.php')){
			require_once($dir.$class_name.'.php');
			return;
		}
	}

	public function register() {
		spl_autoload_register(array($this, 'load'));
	}

	public function unregister() {
		spl_autoload_unregister(array($this, 'load'));
	}

	public function __construct($app) {
		$this->setApp($app);
	}

	public function getApp() {
		return $this->app;
	}

	public function setApp($app) {
		$this->app = $app;
		return $this;
	}

}
