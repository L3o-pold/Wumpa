<?php

namespace Wumpa\Component\Routing\Loader;

use Wumpa\Component\Routing\RoutingTable;
use Wumpa\Component\Routing\Route;
use Wumpa\Component\Exception\Exception\FileNotFoundException;
use Wumpa\Component\Exception\Exception\ConfigErrorException;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class PhpLoader implements LoaderInterface {

	private $file;

	public function load() {
		if(!file_exists($this->getFile()))
			throw new FileNotFoundException($this->getFile());

		$routes = require_once $this->getFile();

		$routingTable = new RoutingTable();

		if(is_null($routes))
			throw new ConfigErrorException("No routes defined in the routes config file");

		foreach($routes as $key => $route) {
			$name = $key;
			$path = $route['path'];
			$controller = $route['controller'];
			if(isset($route['requirements']))
				$requirements = $route['requirements'];
			else
				$requirements = null;

			$routingTable->add(new Route($name, $path, $controller, $requirements));
		}

		return $routingTable;
	}

	public function __construct($file) {
		$this->setFile($file);
	}

	public function getFile() {
		return $this->file;
	}

	public function setFile($file) {
		$this->file = $file;
		return $this;
	}

}
