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
		$requestURI = $_SERVER['REQUEST_URI'];
		$this->setUrl($requestURI);

		if(!!$posGet = strpos($requestURI, '?'))
			$requestURI = substr_replace($requestURI, '', $posGet);
		$requestURI = rtrim($requestURI, '/');

		$requestURI = explode('/', $requestURI);
		$scriptName = explode('/', $_SERVER['SCRIPT_NAME']);

		for($i=0; $i<sizeof($scriptName) && isset($requestURI[$i])  && $requestURI[$i] == $scriptName[$i] ; $i++);

		$nodes = (empty(array_values(array_slice($requestURI,$i))) ? array('') : array_slice($requestURI,$i));
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
