<?php

namespace Wumpa\Component\Routing;

/**
 * Define a request.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Request {

	private $url;
	private $nodes = array();

	public function __construct() {
		$this->setUrl($_SERVER['REQUEST_URI']);

		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$scriptName = explode('/', $_SERVER['SCRIPT_NAME']);

		for($i=0; $i<sizeof($scriptName) && $requestURI[$i] == $scriptName[$i]; $i++);

		$nodes = array_values(array_slice($requestURI,$i));

		$this->setNodes($nodes);
	}

	public function getNodes() {
		return $this->nodes;
	}

	public function setNodes($nodes) {
		$this->nodes = $nodes;
		return $this;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

}
