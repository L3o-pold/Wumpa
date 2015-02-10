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
		$dirs = array(
			$this->getApp()->getIndexDir(),
			$this->getApp()->getControllerDir(),
			$this->getApp()->getModelDir(),
			$this->getApp()->getViewDir(),
			$this->getApp()->getTemplatesDir(),
		);

		foreach($dirs as $dir) {
			if(file_exists($dir.$class_name . '.php')) {
				require_once($dir.$class_name . '.php');
				return;
			}
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
