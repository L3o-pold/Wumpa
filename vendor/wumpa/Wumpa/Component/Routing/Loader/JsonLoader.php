<?php

namespace Wumpa\Component\Routing\Loader;

use Wumpa\Component\Routing\RoutingTable;
use Wumpa\Component\Routing\Route;
use Wumpa\Component\App\App;
use Wumpa\Component\Exception\Exception\FileNotFoundException;
use Wumpa\Component\Exception\Exception\ConfigErrorException;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class JsonLoader implements LoaderInterface {

	private $file;

	public function load() {
		if(!file_exists(App::get(App::CONFIG).$this->getFile()))
			throw new FileNotFoundException(App::get(App::CONFIG).$this->getFile());

		$source = file_get_contents(App::get(Path::CONFIG).$this->getFile());
		$routes = json_decode($source, true);

		if(is_null($routes))
			throw new ConfigErrorException("No routes defined in the routes config file");

		$routingTable = new RoutingTable();

		foreach ($routes['routes'] as $route) {
			if(!isset($route['requirements']))
				$route['requirements'] = null;

			$routingTable->add(new Route($route['name'], $route['path'], $route['controller'], $route['requirements']));
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
